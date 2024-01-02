<?php
namespace Delorius\Iterators;

use Delorius\Utils\Callback;

/**
 * Applies the callback to the elements of the inner iterator.
 *

 */
class Mapper extends \IteratorIterator
{
	/** @var callable */
	private $callback;


	public function __construct(\Traversable $iterator, $callback)
	{
		parent::__construct($iterator);
        $this->callback = Callback::check($callback);
	}



	public function current()
	{
        return call_user_func($this->callback, parent::current(), parent::key());
	}

}
