<?php
namespace Delorius\DataBase\Query\Builder;
/**
 * DataBase query builder for INSERT statements. See [Query Builder](/DataBase/query/builder) for usage and examples.
 *
 * @package    Kohana/DataBase
 * @category   Query
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */
use Delorius\DataBase\Query\Builder;
use Delorius\DataBase\DataBase;


class Insert extends Builder {

	// INSERT INTO ...
	protected $_table;

	// (...)
	protected $_columns = array();

	// VALUES (...)
	protected $_values = array();

	/**
	 * Set the table and columns for an insert.
	 *
	 * @param   mixed  $table    table name or array($table, $alias) or object
	 * @param   array  $columns  column names
	 * @return  void
	 */
	public function __construct($table = NULL, array $columns = NULL)
	{
		if ($table)
		{
			// Set the inital table name
			$this->_table = $table;
		}

		if ($columns)
		{
			// Set the column names
			$this->_columns = $columns;
		}

		// Start the query with no SQL
		return parent::__construct(DataBase::INSERT, '');
	}

	/**
	 * Sets the table to insert into.
	 *
	 * @param   mixed  $table  table name or array($table, $alias) or object
	 * @return  $this
	 */
	public function table($table)
	{
		$this->_table = $table;

		return $this;
	}

	/**
	 * Set the columns that will be inserted.
	 *
	 * @param   array  $columns  column names
	 * @return  $this
	 */
	public function columns(array $columns)
	{
		$this->_columns = $columns;

		return $this;
	}

	/**
	 * Adds or overwrites values. Multiple value sets can be added.
	 *
	 * @param   array   $values  values list
	 * @param   ...
	 * @return  $this
	 */
	public function values(array $values)
	{
		if ( ! is_array($this->_values))
		{
			throw new \Exception('INSERT INTO ... SELECT statements cannot be combined with INSERT INTO ... VALUES');
		}

		// Get all of the passed values
		$values = func_get_args();

		$this->_values = array_merge($this->_values, $values);

		return $this;
	}

	/**
	 * Use a sub-query to for the inserted values.
	 *
	 * @param   object  $query  DataBase\Query of SELECT type
	 * @return  $this
	 */
	public function select(DataBase_Query $query)
	{
		if ($query->type() !== DataBase::SELECT)
		{
			throw new \Exception('Only SELECT queries can be combined with INSERT queries');
		}

		$this->_values = $query;

		return $this;
	}

	/**
	 * Compile the SQL query and return it.
	 *
	 * @param   mixed  $db  DataBase instance or name of instance
	 * @return  string
	 */
	public function compile($db = NULL)
	{
		if ( ! is_object($db))
		{
			// Get the DataBase instance
			$db = DataBase::instance($db);
		}

		// Start an insertion query
		$query = 'INSERT INTO '.$db->quote_table($this->_table);

		// Add the column names
		$query .= ' ('.implode(', ', array_map(array($db, 'quote_column'), $this->_columns)).') ';

		if (is_array($this->_values))
		{
			// Callback for quoting values
			$quote = array($db, 'quote');

			$groups = array();
			foreach ($this->_values as $group)
			{
				foreach ($group as $offset => $value)
				{
					if ((is_string($value) AND array_key_exists($value, $this->_parameters)) === FALSE)
					{
						// Quote the value, it is not a parameter
						$group[$offset] = $db->quote($value);
					}
				}

				$groups[] = '('.implode(', ', $group).')';
			}

			// Add the values
			$query .= 'VALUES '.implode(', ', $groups);
		}
		else
		{
			// Add the sub-query
			$query .= (string) $this->_values;
		}

		$this->_sql = $query;

		return parent::compile($db);
	}

	public function reset()
	{
		$this->_table = NULL;

		$this->_columns =
		$this->_values  = array();

		$this->_parameters = array();

		$this->_sql = NULL;

		return $this;
	}

}
