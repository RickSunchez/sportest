<?php
namespace Delorius\DataBase\Result;
/**
 * Object used for caching the results of select queries.  See [Results](/DataBase/results#select-cached) for usage and examples.
 *
 * @package    Kohana/DataBase
 * @category   Query/Result
 * @author     Kohana Team
 * @copyright  (c) 2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */

use Delorius\DataBase\Result;

class Cached extends Result {

	public function __construct(array $result, $sql, $as_object = NULL)
	{
		parent::__construct($result, $sql, $as_object);

		// Find the number of rows in the result
		$this->_total_rows = count($result);
	}

	public function __destruct()
	{
		// Cached results do not use resources
	}

	public function cached()
	{
		return $this;
	}

	public function seek($offset)
	{
		if ($this->offsetExists($offset))
		{
			$this->_current_row = $offset;

			return TRUE;
		}
		else
		{
			return FALSE;
		}
	}

	public function current()
	{
		// Return an array of the row
		return $this->valid() ? $this->_result[$this->_current_row] : NULL;
	}

}
