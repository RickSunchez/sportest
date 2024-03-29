<?php
namespace Delorius\ComponentModel;



/**
 * Recursive component iterator. See Container::getComponents().
 *
 *
 * @internal
 */
class RecursiveComponentIterator extends \RecursiveArrayIterator implements \Countable
{

	/**
	 * Has the current element has children?
	 * @return bool
	 */
	public function hasChildren()
	{
		return $this->current() instanceof IContainer;
	}



	/**
	 * The sub-iterator for the current element.
	 * @return \RecursiveIterator
	 */
	public function getChildren()
	{
		return $this->current()->getComponents();
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
