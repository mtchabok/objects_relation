<?php
/**
 * Created by PhpStorm.
 * Project: mtchabok_objects_relation
 * User: mtchabok
 * Date: 2019-06-04
 * Time: 7:50 PM
 */

namespace Mtchabok\ObjectsRelation;

/**
 * Trait RelationChild
 * @package Mtchabok\ObjectsRelation
 * @uses Relation
 */
trait RelationChild
{

	/** @return bool */
	public function hasChild() :bool
	{ return $this->hasRelation('child'); }

	/**
	 * @param string|object $child
	 * @return bool
	 */
	public function isChild($child) :bool
	{ return $this->isRelation($child, 'child'); }

	/**
	 * @param string|null $childId=null
	 * @return $this|null
	 */
	public function getChild(string $childId = null)
	{ return $this->getRelation($childId, 'child'); }

	/** @return array */
	public function getChildes()
	{ return $this->getRelations('child'); }

	/**
	 * @param object $child
	 * @return $this
	 */
	public function addChild($child)
	{ return $this->addRelation($child, 'child'); }

	/**
	 * @param object|array $child
	 * @return $this
	 */
	public function setChild($child)
	{ return $this->setRelations($child, 'child'); }

	/**
	 * @param object|string $child
	 * @return $this
	 */
	public function removeChild($child)
	{ return $this->removeRelation($child, 'child'); }

	/** @return $this */
	public function deleteChildes()
	{ return $this->deleteRelation('child'); }



	public function __construct()
	{
		$this->addRelationType('child', 'parent');
	}
}
