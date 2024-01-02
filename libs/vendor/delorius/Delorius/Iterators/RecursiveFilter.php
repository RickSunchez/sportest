<?php
namespace Delorius\Iterators;

use Delorius\Utils\Callback;

/**
 * Callback recursive iterator filter.
 *

 */
class RecursiveFilter extends \FilterIterator implements \RecursiveIterator
{
	/** @var callable */
	private $callback;

	/** @var callable */
	private $childrenCallback;


	public function __construct(\RecursiveIterator $iterator, $callback, $childrenCallback = NULL)
	{
		parent::__construct($iterator);
		$this->callback = $callback === NULL ? NULL : new Callback($callback);
		$this->childrenCallback = $childrenCallback === NULL ? NULL : new Callback($childrenCallback);
	}



	public function accept()
	{
		return $this->callback === NULL || $this->callback->invoke_($this);
	}



	public function hasChildren()
	{
		return $this->getInnerIterator()->hasChildren()
			&& ($this->childrenCallback === NULL || $this->childrenCallback->invoke_($this));
	}



	public function getChildren()
	{
		return new static($this->getInnerIterator()->getChildren(), $this->callback, $this->childrenCallback);
	}

}
