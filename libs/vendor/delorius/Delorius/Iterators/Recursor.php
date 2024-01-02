<?php
namespace Delorius\Iterators;

/**
 * Generic recursive iterator.
 *

 */
class Recursor extends \IteratorIterator implements \RecursiveIterator, \Countable
{

	/**
	 * Has the current element has children?
	 * @return bool
	 */
	public function hasChildren()
	{
		$obj = $this->current();
		return ($obj instanceof \IteratorAggregate && $obj->getIterator() instanceof \RecursiveIterator)
			|| $obj instanceof \RecursiveIterator;
	}



	/**
	 * The sub-iterator for the current element.
	 * @return \RecursiveIterator
	 */
	public function getChildren()
	{
		$obj = $this->current();
		return $obj instanceof \IteratorAggregate ? $obj->getIterator() : $obj;
	}



	/**
	 * Returns the count of elements.
	 * @return int
	 */
	public function count()
	{
		return iterator_count($this);
	}

}
