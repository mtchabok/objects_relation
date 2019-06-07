<?php
/**
 * Created by PhpStorm.
 * Project: mtchabok_objects_relation
 * User: mtchabok
 * Date: 2019-06-04
 * Time: 12:51 PM
 */

namespace Mtchabok\ObjectsRelation;


trait Relation
{
	/** @var string */
	protected $_objectRelationUniqueId = '';
	/** @var array */
	protected $_objectRelations = [];
	/** @var bool */
	protected $_objectRelationTypeAutoAdd = false;
	/** @var array */
	protected $_objectRelationReturningType = [];


	/** @return string */
	public function getObjectUniqueId() :string
	{ return $this->_objectRelationUniqueId ?$this->_objectRelationUniqueId :($this->_objectRelationUniqueId = uniqid()); }







	/**
	 * @param string|array $type
	 * @param string|array|null $returnType=null
	 * @return $this|false
	 */
	public function addRelationType($type, $returnType = null)
	{
		foreach (($types = is_array($type) ?$type :[$type]) as $type)
			if(!is_string($type) || !$type) return false;
		if(null!==$returnType){
			if(!((is_string($returnType) && $returnType) || is_callable($returnType))) return false;
		}
		foreach ($types as $type){
			if(!array_key_exists($type, $this->_objectRelations))
				$this->_objectRelations[$type] = [];
			if(null!==$returnType)
				$this->_objectRelationReturningType[$type] = $returnType;
		}
		return $this;
	}



	/**
	 * @param string|object|null $relation=null
	 * @return array
	 */
	public function getRelationTypes($relation = null) :array
	{
		$types = [];
		if(null===$relation) $types = array_keys($this->_objectRelations);
		else{
			if(is_object($relation) && method_exists($relation, 'getObjectUniqueId'))
				$relation = $relation->getObjectUniqueId();
			if(is_string($relation) && $relation) {
				foreach ($this->getRelationTypes() as $type)
					if (array_key_exists($relation, $this->_objectRelations[$type]))
						$types[] = $type;
			}
		} return $types;
	}







	/**
	 * @param string|null $type=null
	 * @return bool
	 */
	public function hasRelation($type = null) :bool
	{
		$hasRelation = false;
		if(null===$type){
			foreach ($this->getRelationTypes() as $type){
				if($hasRelation = !empty($this->_objectRelations[$type])) break;
			}
		}elseif(is_string($type) && $type){
			$hasRelation = !empty($this->_objectRelations[$type]);
		}
		return $hasRelation;
	}

	/**
	 * @param string|object $relation
	 * @param string|array|null $type=null
	 * @return bool
	 */
	public function isRelation($relation, $type = null) :bool
	{
		$relationId = is_string($relation) ?$relation
			:((is_object($relation) && method_exists($relation, 'getObjectUniqueId')) ?$relation->getObjectUniqueId() :'');
		if(!$relationId) return false;
		$types = is_array($type) ?$type
			:((is_string($type) && $type) ?[$type] :$this->getRelationTypes());
		foreach ($types as $type){
			if(isset($this->_objectRelations[$type][$relationId]))
				return true;
		} return false;
	}

	/**
	 * @param string|null $relationId=null
	 * @param string|null $type=null
	 * @return $this|null
	 */
	public function getRelation(string $relationId = null , string $type = null)
	{
		if(is_string($type) && $type){
			if(in_array($type, $this->getRelationTypes())){
				if($relationId)
					return empty($this->_objectRelations[$type][$relationId]) ?null :$this->_objectRelations[$type][$relationId];
				else{
					foreach ($this->_objectRelations[$type] as $relation)
						return $relation;
				}
			}
		}elseif($relationId){
			foreach ($this->getRelationTypes() as $type)
				if(!empty($this->_objectRelations[$type][$relationId]))
					return $this->_objectRelations[$type][$relationId];
		}else{
			foreach ($this->getRelationTypes() as $type)
				foreach ($this->_objectRelations[$type] as $relation)
					return $relation;
		}
		return null;
	}

	/**
	 * @param string|array|null $type
	 * @return array
	 */
	public function getRelations($type = null) :array
	{
		$relations = [];
		$types = is_array($type) ?$type
			:((is_string($type) && $type) ?[$type] :$this->getRelationTypes());
		foreach ($types as $type)
			if(isset($this->_objectRelations[$type]))
				$relations = array_merge($relations, $this->_objectRelations[$type]);
		return $relations;
	}

	/**
	 * @param string|array|null $type
	 * @return array
	 */
	public function getRelationsIds($type = null) :array
	{
		$relationsIds = [];
		$types = is_array($type) ?$type
			:((is_string($type) && $type) ?[$type] :$this->getRelationTypes());
		foreach ($types as $type){
			if (isset($this->_objectRelations[$type])) {
				foreach ($this->_objectRelations[$type] as $relationId => $relation)
					if(!in_array($relationId, $relationsIds))
						$relationsIds[] = $relationId;
			}
		} return $relationsIds;
	}

	/**
	 * @param object|array $relation
	 * @param string|array $type
	 * @return $this|false
	 */
	public function addRelation($relation, $type)
	{
		/** @var Relation $relation */
		if(is_array($relation)){
			foreach ($relation as $r)
				if(!$this->addRelation($r, $type)) return false;
			return $this;
		}
		if(!is_object($relation) || !method_exists($relation, 'getObjectUniqueId')) return false;
		$types = is_array($type) ?$type :[$type];
		foreach ($types as $type){
			if(!is_string($type) || !$type)
				return false;
			if(!in_array($type, $this->getRelationTypes()) && !$this->_objectRelationTypeAutoAdd)
				return false;
		}
		$relationId = $relation->getObjectUniqueId();
		foreach ($types as $type){
			if(!$this->addRelationType($type)) return false;
			if(!array_key_exists($relationId, $this->_objectRelations[$type])){
				$this->_objectRelations[$type][$relationId] = $relation;
				$returningType = $this->_relationReturningType($type, $relation);
				if($returningType && !$relation->addRelation($this, $returningType))
					return false;
			}
		}
		return $this;
	}

	/**
	 * @param string|object|array $relation
	 * @param string|array|null $type=null
	 * @return $this
	 */
	public function removeRelation($relation, $type = null)
	{
		if(is_array($relation)){
			foreach ($relation as $r) $this->removeRelation($r, $type);
			return $this;
		}
		$relationId = is_string($relation) ?$relation
			:((is_object($relation) && method_exists($relation, 'getObjectUniqueId')) ?$relation->getObjectUniqueId() :'');
		if (!$relationId)
			return $this;
		$types = is_array($type) ?$type
			:((is_string($type) && $type) ?[$type] :$this->getRelationTypes());
		foreach ($types as $type){
			if(isset($this->_objectRelations[$type][$relationId])){
				/** @var Relation $relation */
				$relation = $this->_objectRelations[$type][$relationId];
				unset($this->_objectRelations[$type][$relationId]);
				$returningType = $this->_relationReturningType($type, $relation);
				$relation->removeRelation($this->getObjectUniqueId(), $returningType ?$returningType :null);
			}
		} return $this;
	}

	/**
	 * @param string|array|null $type
	 * @return $this
	 */
	public function clearRelations($type = null)
	{
		$relationsIds = $this->getRelationsIds($type);
		foreach ($relationsIds as $relationId)
			$this->removeRelation($relationId);
		return $this;
	}



	/**
	 * @param object|array $relation
	 * @param string|array $type
	 * @return $this|false
	 */
	public function setRelations($relation, $type)
	{
		$relations = [];
		foreach (is_array($relation) ?$relation :[$relation] as $relation){
			if(!is_object($relation) || !method_exists($relation, 'getObjectUniqueId')) return false;
			$relationId = $relation->getObjectUniqueId();
			if(!isset($relations[$relationId])) $relations[$relationId] = $relation;
		}
		if(!$types = is_array($type) ?$type :[$type]) return false;
		foreach ($types as $type){
			if(!is_string($type) || !$type)
				return false;
			if(!in_array($type, $this->getRelationTypes()) && !$this->_objectRelationTypeAutoAdd)
				return false;
		}
		foreach ($types as $type){
			if(!$this->addRelationType($type)) return false;
			$oldRelations = $this->getRelations($type);
			foreach ($relations as $relationId=>$relation){
				if(!$this->addRelation($relation, $type)) return false;
				unset($oldRelations[$relationId]);
			}
			foreach ( $oldRelations as $relationId=>$relation )
				$this->removeRelation($relationId);
		}
		return $this;
	}

	/**
	 * @param string|array|null $type
	 * @return $this
	 */
	public function deleteRelation($type = null)
	{ return $this->clearRelations($type); }







	/**
	 * @param string $type
	 * @param object $relation
	 * @return string
	 */
	protected function _relationReturningType(string $type, $relation) :string
	{
		$returningType = '';
		if(!empty($this->_objectRelationReturningType[$type])){
			if(is_callable($this->_objectRelationReturningType[$type]))
				$returningType = (string) call_user_func($this->_objectRelationReturningType[$type], $type, $relation);
			elseif (is_string($this->_objectRelationReturningType[$type]))
				$returningType = $this->_objectRelationReturningType[$type];
		}
		return $returningType;
	}
}
