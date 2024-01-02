<?php
namespace Delorius\Iterators;

use Delorius\Utils\Callback;

/**
 * Callback iterator filter.
 *

 */
class Filter extends \FilterIterator
{
	/** @var callable */
	private $callback;


	public function __construct(\Iterator $iterator, $callback)
	{
		parent::__construct($iterator);
		$this->callback = new Callback($callback);
	}



	public function accept()
	{
		return $this->callback->invoke_($this);
	}

}
