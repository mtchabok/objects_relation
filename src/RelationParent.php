<?php
/**
 * Created by PhpStorm.
 * Project: mtchabok_objects_relation
 * User: mtchabok
 * Date: 2019-06-04
 * Time: 1:03 PM
 */

namespace Mtchabok\ObjectsRelation;

/**
 * Trait RelationParent
 * @package Mtchabok\ObjectsRelation
 * @uses Relation
 */
trait RelationParent
{

	/** @return bool */
	public function hasParent() :bool
	{ return $this->hasRelation('parent'); }

	/**
	 * @param string|object $parent
	 * @return bool
	 */
	public function isParent($parent) :bool
	{ return $this->isRelation($parent, 'parent'); }

	/**
	 * @param string|null $parentId=null
	 * @return $this|null
	 */
	public function getParent(string $parentId = null)
	{ return $this->getRelation($parentId, 'parent'); }

	/** @return array */
	public function getParents()
	{ return $this->getRelations('parent'); }

	/**
	 * @param object $parent
	 * @return $this
	 */
	public function addParent($parent)
	{ return $this->addRelation($parent, 'parent'); }

	/**
	 * @param object|array $parent
	 * @return $this
	 */
	public function setParent($parent)
	{ return $this->setRelations($parent, 'parent'); }

	/**
	 * @param object|string $parent
	 * @return $this
	 */
	public function removeParent($parent)
	{ return $this->removeRelation($parent, 'parent'); }

	/** @return $this */
	public function deleteParents()
	{ return $this->deleteRelation('parent'); }



	public function __construct()
	{
		$this->addRelationType('parent', 'child');
	}

}
