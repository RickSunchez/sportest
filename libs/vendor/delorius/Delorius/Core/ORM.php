<?php

namespace Delorius\Core;

/**
 * [Object Relational Mapping][ref-orm] (ORM) is a method of abstracting DataBase
 * access to standard PHP calls. All table rows are represented as model objects,
 * with object properties representing row data. ORM in Kohana generally follows
 * the [Active Record][ref-act] pattern.
 *
 * [ref-orm]: http://wikipedia.org/wiki/Object-relational_mapping
 * [ref-act]: http://wikipedia.org/wiki/Active_record
 *
 * @package    Kohana/ORM
 * @author     Kohana Team
 * @copyright  (c) 2007-2012 Kohana Team
 * @license    http://kohanaframework.org/license
 *
 * переписан Delorius Team
 */

use Delorius\Behaviors\Helpers;
use Delorius\Caching\Cache;
use Delorius\Exception\OrmValidationError;
use Delorius\DataBase\DB;
use Delorius\DataBase\DataBase;
use Delorius\Tools\Debug\Profiler;
use Delorius\Utils\Arrays;
use Delorius\Utils\Json;
use Delorius\Utils\Strings;
use Delorius\Exception\Error;

class ORM extends Object implements \Iterator
{
    /** @var array of function(ORM $orm); */
    public $onBeforeSave;
    public $onAfterSave;

    /** @var array of function(ORM $orm); */
    public $onBeforeDelete;
    public $onAfterDelete;

    /** @var array of function(ORM $orm); */
    public $onBeforeFind;
    public $onAfterFind;


    /**
     * Stores column information for ORM models
     * @var array
     */
    protected static $_column_cache = array();

    /**
     * Initialization storage for ORM models
     * @var array
     */
    protected static $_init_cache = array();

    /**
     * Current object
     * @var array
     */
    protected $_object = array();

    /**
     * @var array
     */
    protected $_changed = array();

    /**
     * @var array
     */
    protected $_original_values = array();
    /**
     * @var bool
     */
    protected $_valid = FALSE;
    /**
     * @var bool
     */
    protected $_loaded = FALSE;
    /**
     * @var bool
     */
    protected $_saved = FALSE;

    /**
     * Model name
     * @var string
     */
    protected $_object_name;

    /**
     * Table name
     * @var string
     */
    protected $_table_name;

    /**
     * Table columns
     * [
     * 'name_column' => array(
     * 'column_name' => 'name_column'
     * 'data_type' => 'int'  // | varchar | tinyint | text     {type} [unsigned]
     * 'is_nullable' => false
     * 'column_default' =>
     * 'display' => 11 //for number int(11)
     * 'exact' => 1 , // for decimal
     * 'numeric_scale' => 2,  // for decimal  (10,2)
     * 'numeric_precision' =>  10, //for decimal (10,2)
     * 'character_maximum_length' => 100,  // for string varchar(100)
     * 'collation_name' => 'utf8_general_ci',
     * 'extra' => 'auto_increment' // auto_increment
     * 'key' => 'PRI' // (pk) |MUL (index)|UNI (unique)
     * ),
     * ]
     * @var array []
     */
    protected $_table_columns = array();
    /**
     * Auto-update columns for updates
     *
     *
     * array(
     * 'column' => 'update_time',
     * 'format' => TRUE, // 'd.m.Y H:i'
     * );
     *
     * INT(10) unsigned `update_time`  //create field mysql
     *
     * @var string
     */
    protected $_updated_column = NULL;


    /**
     * Установившийся поля
     * @var array
     */
    protected $_table_columns_set = array();

    /**
     * Auto-update columns for creation
     *
     * array(
     * 'column' => 'creation_time',
     * 'format' => TRUE, // 'd.m.Y H:i'
     * );
     *
     * INT(10) unsigned `creation_time` //create field mysql
     *
     *
     * @var string
     */
    protected $_created_column = NULL;

    /**
     * Table primary key
     * @var string
     */
    protected $_primary_key = 'id';

    /**
     * Primary key value
     * @var mixed
     */
    protected $_primary_key_value;

    /**
     * Table field for config
     *
     *
     * TEXT `{name_config}` //create field mysql
     *
     * @var string {name_config}
     */
    protected $_config_key = NULL;

    /**
     * Table value for config
     * @var array
     */
    protected $_config_key_value = array();

    /**
     * DataBase Object
     * @var DataBase
     */
    protected $_db = NULL;

    /**
     * DataBase config name
     * @var String
     */
    protected $_db_config = NULL;

    /**
     * DataBase methods applied
     * @var array
     */
    protected $_db_applied = array();

    /**
     * DataBase methods pending
     * @var array
     */
    protected $_db_pending = array();

    /**
     * Reset builder
     * @var bool
     */
    protected $_db_reset = TRUE;

    /**
     * DataBase query builder
     * @var \Delorius\DataBase\Query\Builder\Select
     */
    protected $_db_builder;

    /**
     * Error message
     * @var array
     */
    protected $_errors = array();

    /**
     * Error name fields
     * @var array
     */
    protected $_error_fields = array();

    /** @var  bool fix отключает фильтр */
    protected $_is_filter;

    /** @var bool */
    protected $_log = false;

    /** @var bool */
    private $_is_array = false;

    /** @var bool */
    private $_init_starter = false;


    /**
     * Constructs a new model and loads a record if given
     * @param mixed $id Parameter for find or object to load
     * @param bool|true $isFilter
     * @throws Error
     */
    public function __construct($id = NULL, $isFilter = true)
    {
        $this->_is_filter = $isFilter;
        $this->_init();
        if ($id !== NULL) {
            if (is_array($id)) {
                foreach ($id as $column => $value) {
                    // Passing an array of column => values
                    $this->where($column, '=', $value);
                }
                $this->find();
            } else {
                // Passing the primary key
                $this->where($this->_table_name . '.' . $this->_primary_key, '=', $id)->find();
            }
        }
        $this->init();
    }

    /**
     * @var array
     * @internal
     */
    protected static $_update_table_columns = array();

    /**
     * @param array $cols
     * @internal
     */
    public static function update_table_columns(array $cols)
    {
        ORM::$_update_table_columns[get_called_class()] = $cols;
    }

    /**
     * @var array
     * @internal
     */
    protected static $_update_behaviors = array();

    /**
     * @param array $behaviors
     * @internal
     */
    public static function update_behaviors(array $behaviors)
    {
        ORM::$_update_behaviors[get_called_class()] = $behaviors;
    }

    protected function init()
    {
        //some code
    }

    /**
     * @param null $id
     * @param bool|true $isFilter
     * @return $this
     */
    public static function model($id = null, $isFilter = true)
    {
        $class = get_called_class();
        return new $class($id, $isFilter);
    }

    /**
     * Insert a new object to the databas
     * @throws Error
     * @return ORM
     */
    protected function create($cache_delete = false)
    {
        if ($this->_loaded)
            throw new Error("Cannot create {$this->_object_name} model because it is already loaded.");

        // Require model validation before saving
        if (!$this->_valid) {
            if ($this->_valid = $this->check() === FALSE) {
                $this->validException();
            }
        }

        $data = array();
        foreach ($this->_changed as $column => $original_value) {
            // Generate list of column => values
            $data[$column] = $this->_object[$column];
        }

        if (is_array($this->_created_column)) {
            // Fill the created column
            $column = $this->_created_column['column'];
            $format = $this->_created_column['format'];
            $data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
        }

        $result = DB::insert($this->_table_name)
            ->columns(array_keys($data))
            ->values(array_values($data))
            ->execute($this->_db);

        $this->_object[$this->_primary_key] = $this->_primary_key_value = $result[0];
        foreach (array_keys($this->_table_columns) as $key) {
            $this->_object[$key] = $this->_object[$key];
        }

        // Object is now loaded and sav_saveded
        $this->_loaded = $this->_saved = TRUE;
        // All changes have been saved
        $this->_changed = array();
        $this->_original_values = $this->_object;
        if ($cache_delete) {
            $this->cache_delete();
        }
        return $this;
    }

    /**
     * Updates a single record or multiple records
     *
     * @chainable
     * @param  Validation $validation Validation object
     * @throws OrmValidationError
     * @return ORM
     */
    protected function update($cache_delete = false)
    {
        if (!$this->_loaded)
            throw new Error("Cannot update {$this->_object_name} model because it is not loaded.");

        // Require model validation before saving
        if (!$this->_valid) {
            if ($this->_valid = $this->check() === FALSE) {
                $this->validException();
            }
        }

        if (empty($this->_changed)) {
            // Nothing to update
            return $this;
        }

        $data = array();
        foreach ($this->_changed as $column => $original_value) {
            $data[$column] = $this->_object[$column];
        }

        if (is_array($this->_updated_column)) {
            // Fill the updated column
            $column = $this->_updated_column['column'];
            $format = $this->_updated_column['format'];

            $data[$column] = $this->_object[$column] = ($format === TRUE) ? time() : date($format);
        }

        // Use primary key value
        $id = $this->pk();

        // Update a single record
        DB::update($this->_table_name)
            ->set($data)
            ->where($this->_primary_key, '=', $id)
            ->execute($this->_db);

        if (isset($data[$this->_primary_key])) {
            // Primary key was changed, reflect it
            $this->_primary_key_value = $data[$this->_primary_key];
        }

        // Object has been saved
        $this->_saved = TRUE;

        // All changes have been saved
        $this->_changed = array();
        $this->_original_values = $this->_object;
        if ($cache_delete) {
            $this->cache_delete();
        }
        return $this;
    }


    /**
     * Updates or Creates the record depending on loaded()
     *
     * @chainable
     * @return ORM
     */
    public function save($cache_delete = false)
    {
        $this->onBeforeSave($this);
        if ($this->loaded()) {
            $res = $this->update($cache_delete);
        } else {
            $res = $this->create($cache_delete);
        }
        $this->onAfterSave($this);
        return $res;
    }

    /**
     * Deletes a single record while ignoring relationships.
     *
     * @chainable
     * @throws Error
     * @return ORM
     */
    public function delete($cache_delete = false)
    {
        if (!$this->_loaded)
            throw new Error("Cannot delete {$this->_object_name} model because it is not loaded.");

        $this->onBeforeDelete($this);
        // Use primary key value
        $id = $this->pk();

        // Delete the object
        DB::delete($this->_table_name)
            ->where($this->_primary_key, '=', $id)
            ->execute($this->_db);
        if ($cache_delete) {
            $this->cache_delete();
        }
        $this->onAfterDelete($this);
        return $this->clear();
    }

    public function cache_delete()
    {
        Environment::getContext()->getService('cache')->clean(array(
            Cache::TAGS => array(
                $this->table_name(),
                $this->object_name(),
            ),
        ));
    }

    /**
     * Returns the value of the primary key
     *
     * @return mixed Primary key
     */
    public function pk()
    {
        return $this->_primary_key_value;
    }

    public function loaded()
    {
        return $this->_loaded;
    }

    public function saved()
    {
        return $this->_saved;
    }

    public function primary_key()
    {
        return $this->_primary_key;
    }

    public function table_name()
    {
        return $this->_table_name;
    }

    public function table_columns()
    {
        return $this->_table_columns;
    }

    /**
     * @param $column
     * @return bool
     */
    public function has($column)
    {
        return ObjectMixin::has($this, $column);
    }

    /**
     * Handles getting of column
     * Override this method to add custom get behavior
     *
     * @param   string $column Column name
     * @throws Error
     * @return mixed
     */
    public function get($column)
    {
        if (array_key_exists($column, $this->_object)) {
            return $this->_object[$column];
        } elseif ($this->_loaded) {
            throw new Error("The $column property does not exist in the " . get_class($this) . " class");
        } else {
            return null;
        }
    }

    /**
     * Handles setting of columns
     * Override this method to add custom set behavior
     *
     * @param  string $column Column name
     * @param  mixed $value Column value
     * @throws Error
     * @return ORM
     */
    public function set($column, $value)
    {
        if (($column == $this->_config_key) && !empty($this->_config_key)) // for config
        {
            if (is_array($value)) {
                $this->_config_key_value = count($value) ? $value : null;
                $this->_object[$column] = count($value) ? Json::encode(Strings::escape_array($value)) : '';
                $this->_changed[$column] = count($value) ? Json::encode(Strings::escape_array($value)) : '';
                $this->_saved = FALSE;
            } else {
                $this->_config_key_value = Json::decode($value);
                $this->_changed[$column] = $this->_object[$column] = $value;
                $this->_saved = FALSE;
            }

        } elseif (
            array_key_exists($column, $this->_object) ||
            !$this->_loaded
        ) {
            $original = $value;
            // Filter the data
            if ($this->_is_filter)
                $value = $this->run_filter($column, $value);
            // See if the data really changed
            if ($value !== $this->_object[$column]) {
                $this->_object[$column] = $value;
                // Data has changed
                $this->_changed[$column] = $original;
                // Object is no longer saved or valid
                $this->_saved = $this->_valid = FALSE;
            }
        } else {
            throw new Error("The $column property does not exist in the " . get_class($this) . " class");
        }
        return $this;
    }


    /**
     * Check whether the model data has been modified.
     * If $field is specified, checks whether that field was modified.
     *
     * @param string $field field to check for changes
     * @return  bool  Whether or not the field has changed
     */
    public function changed($field = NULL)
    {
        return ($field === NULL)
            ? $this->_changed
            : array_key_exists($field, $this->_changed);
    }

    /**
     * Set values from an array with support for one-one relationships.  This method should be used
     * for loading in post data, etc.
     *
     * @param  array $values Array of column => val
     * @param  array $expected Array of keys to take from $values
     * @return ORM
     */
    public function values(array $values, array $expected = NULL)
    {
        // Default to expecting everything except the primary key
        if ($expected === NULL) {
            $expected = array_keys($this->_table_columns);

            // Don't set the primary key by default
            unset($values[$this->_primary_key]);
        }

        foreach ($expected as $key => $column) {
            if (is_string($key)) {
                // isset() fails when the value is NULL (we want it to pass)
                if (!array_key_exists($key, $values))
                    continue;

                // Try to set values to a related model
                $this->{$key}->values($values[$key], $column);
            } else {
                // isset() fails when the value is NULL (we want it to pass)
                if (!array_key_exists($column, $values))
                    continue;

                // Update the column, respects __set()
                $this->$column = $values[$column];
            }
        }
        return $this;
    }

    /**
     * Validates the current model's data
     *
     * @return ORM
     */
    protected function check()
    {
        $this->_marge_columns_set();
        $this->_errors = array();
        $this->_error_fields = array();
        if (count($arRules = $this->rules())) {
            foreach ($this->_changed as $name => $value) {
                $passed = true;
                if (!isset($arRules[$name]))
                    continue;

                foreach ($arRules[$name] as $key => $rules) {
                    list($rule, $params, $message) = $rules;
                    foreach ($params as $key => $param) {
                        if (is_string($param) AND $param == ':value') {
                            $params[$key] = $value;
                        }
                    }

                    if (is_array($rule)) {
                        $passed = call_user_func_array($rule, $params);
                    } elseif (!is_string($rule)) {
                        $passed = call_user_func_array($rule, $params);
                    } elseif (method_exists('\\Delorius\\Utils\\Valid', $rule)) {
                        $method = new \ReflectionMethod('\\Delorius\\Utils\\Valid', $rule);
                        $passed = $method->invokeArgs(NULL, $params);
                    } elseif (strpos($rule, '::') === FALSE) {
                        $function = new \ReflectionFunction($rule);
                        $passed = $function->invokeArgs($params);
                    } else {
                        list($class, $method) = explode('::', $rule, 2);
                        $method = new \ReflectionMethod($class, $method);
                        $passed = $method->invokeArgs(NULL, $params);
                    }

                    if (!$passed) {
                        if ($message)
                            $this->_errors[] = $message;

                        $this->_error_fields[$name]['rules'] = $rule;
                        $this->_error_fields[$name]['message'] = $message;
                        $this->_error_fields[$name]['value'] = $value;
                        break;
                    }
                }

            }
        }
        return empty($this->_error_fields);
    }


    /**
     * Returns the values of this object as an array
     *
     * @return array
     */
    public function as_array()
    {
        if (!$this->loaded()) {
            return array();
        }
        $object = array();
        foreach ($this->_object as $column => $value) {

            if ($this->_config_key == $column) {
                $object[$column] = $this->_config_key_value;
            } else {
                // Call __get for any user processing
                $object[$column] = $this->__get($column);
            }
        }
        $object['id'] = $this->pk();
        return $object;
    }


    /**
     * Clears query builder.  Passing FALSE is useful to keep the existing
     * query conditions for another query.
     *
     * @param bool $next Pass FALSE to avoid resetting on the next call
     * @return ORM
     */
    public function reset($next = TRUE)
    {
        if ($next AND $this->_db_reset) {
            $this->_db_pending = array();
            $this->_db_applied = array();
            $this->_db_builder = NULL;
        }
        // Reset on the next call?
        $this->_db_reset = $next;
        return $this;
    }

    /**
     * Unloads the current object and clears the status.
     *
     * @chainable
     * @return ORM
     */
    public function clear()
    {
        // Replace the object and reset the object status
        $this->_changed = $this->_original_values = $this->_errors = $this->_error_fields = array();

        // Reset primary key
        $this->_primary_key_value = NULL;

        // Reset the loaded state
        $this->_loaded = FALSE;

        $this->reset();

        return $this;
    }

    public function getErrors()
    {
        return $this->_errors;
    }

    public function getErrorFields()
    {
        return $this->_error_fields;
    }

    public function getConfig()
    {
        if ($this->_config_key === NULL)
            throw new Error('Field is not set to config');

        return (array)$this->_config_key_value;
    }

    public function setConfig(array $config)
    {
        if ($this->_config_key === NULL)
            throw new Error('Field is not set to config');

        $this->set($this->_config_key, $config);
        return $this;
    }

    /**
     * Count the number of records in the table.
     *
     * @return integer
     */
    public function count_all()
    {
        $selects = array();

        foreach ($this->_db_pending as $key => $method) {
            if ($method['name'] == 'select') {
                // Ignore any selected columns for now
                $selects[] = $method;
                unset($this->_db_pending[$key]);
            }
        }

        $this->_build(DataBase::SELECT);

        $result = $this->_db_builder->from($this->_table_name)
            ->select(array(DB::expr('COUNT(*)'), 'records_found'))
            ->execute($this->_db);
        if ($result->count() > 1) { // group by
            $records = $result->count();
        } else {
            $records = $result->get('records_found');
        }

        // Add back in selected columns
        $this->_db_pending += $selects;

        $this->reset();

        // Return the total number of records in a table
        return $records;
    }

    /**
     * Proxy method to DataBase list_columns.
     *
     * @return array
     */
    public function list_columns()
    {
        // Proxy to DataBase
        return $this->_db->list_columns($this->_table_name);
    }

    /**
     * Proxy method to DataBase list_tables is ORM->_table_name.
     *
     * @return bool
     */
    public function issetTable()
    {
        return sizeof($this->_db->list_tables($this->_table_name)) == 0 ? false : true;
    }

    /**
     * Returns last executed query
     *
     * @return string
     */
    public function last_query()
    {
        return $this->_db->last_query;
    }

    /**
     * @return $this
     */
    public function log()
    {
        $this->_log = true;
        return $this;
    }


    public function original_values()
    {
        return $this->_original_values;
    }

    public function created_column()
    {
        return $this->_created_column;
    }

    public function updated_column()
    {
        return $this->_updated_column;
    }

    public function object()
    {
        return $this->_object;
    }

    public function object_name()
    {
        return $this->_object_name;
    }


    /**
     * Finds and loads a single DataBase row into the object.
     *
     * @chainable
     * @throws Error
     * @return $this
     */
    public function find()
    {
        if ($this->_loaded)
            throw new Error('Method find() cannot be called on loaded objects');
        $this->_build(DataBase::SELECT);
        return $this->_load_result(FALSE);
    }

    /**
     * Finds multiple DataBase rows and returns an iterator of the rows found.
     *
     * @throws Error
     * @return \Delorius\Core\ORM | \Delorius\DataBase\Result
     */
    public function find_all()
    {
        if ($this->_loaded)
            throw new Error('Method find_all() cannot be called on loaded objects');
        $this->_build(DataBase::SELECT);
        return $this->_load_result(TRUE);
    }


    /**
     * Alias of and_where()
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $op logic operator
     * @param   mixed $value column value
     * @return  $this
     */
    public function where($column, $op, $value)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'where',
            'args' => array($column, $op, $value),
        );

        return $this;
    }

    /**
     * Creates a new "AND WHERE" condition for the query.
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $op logic operator
     * @param   mixed $value column value
     * @return  $this
     */
    public function and_where($column, $op, $value)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'and_where',
            'args' => array($column, $op, $value),
        );

        return $this;
    }

    /**
     * Creates a new "OR WHERE" condition for the query.
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $op logic operator
     * @param   mixed $value column value
     * @return  $this
     */
    public function or_where($column, $op, $value)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'or_where',
            'args' => array($column, $op, $value),
        );

        return $this;
    }

    /**
     * Alias of and_where_open()
     *
     * @return  $this
     */
    public function where_open()
    {
        return $this->and_where_open();
    }

    /**
     * Opens a new "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function and_where_open()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'and_where_open',
            'args' => array(),
        );

        return $this;
    }

    /**
     * Opens a new "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function or_where_open()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'or_where_open',
            'args' => array(),
        );

        return $this;
    }

    /**
     * Closes an open "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function where_close()
    {
        return $this->and_where_close();
    }

    /**
     * Closes an open "AND WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function and_where_close()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'and_where_close',
            'args' => array(),
        );

        return $this;
    }

    /**
     * Closes an open "OR WHERE (...)" grouping.
     *
     * @return  $this
     */
    public function or_where_close()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'or_where_close',
            'args' => array(),
        );

        return $this;
    }


    /**
     *  `column_name` IS [NOT] NULL
     *
     * @return  $this
     */
    public function is_null($column, $null = true)
    {
        if (!$null)
            $null = 'IS NOT';
        else
            $null = 'IS';
        $this->where($column, $null, NULL);

        return $this;
    }

    /**
     * Applies sorting with "ORDER BY ..."
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $direction direction of sorting
     * @return  $this
     */
    public function order_by($column, $direction = NULL)
    {
        // Add pending DataBase call which is executed after query type is determined
        if (is_string($column) && strpos($column, '.') === false) {
            $column = $this->table_name() . '.' . $column;
        }

        $this->_db_pending[] = array(
            'name' => 'order_by',
            'args' => array($column, $direction),
        );

        return $this;
    }

    /**
     * Applies sorting with "ORDER BY `primary_key` ..."
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $direction direction of sorting
     * @return  $this
     */
    public function order_pk($direction = NULL)
    {
        $this->order_by($this->primary_key(), $direction);
        return $this;
    }

    /**
     * Applies sorting with "ORDER BY `update_time` ..."
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $direction direction of sorting
     * @return  $this
     */
    public function order_updated($direction = NULL)
    {
        if ($this->_updated_column != null) {
            $this->order_by($this->_updated_column['column'], $direction);
        }
        return $this;
    }

    /**
     * Applies sorting with "ORDER BY `creation_time` ..."
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $direction direction of sorting
     * @return  $this
     */
    public function order_created($direction = NULL)
    {
        if ($this->_created_column != null) {
            $this->order_by($this->_created_column['column'], $direction);
        }
        return $this;
    }

    /**
     * Return up to "LIMIT ..." results
     *
     * @param   integer $number maximum results to return
     * @return  $this
     */
    public function limit($number)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'limit',
            'args' => array($number),
        );

        return $this;
    }

    /**
     * Enables or disables selecting only unique columns using "SELECT DISTINCT"
     *
     * @param   boolean $value enable or disable distinct columns
     * @return  $this
     */
    public function distinct($value)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'distinct',
            'args' => array($value),
        );

        return $this;
    }

    /**
     * Choose the columns to select from.
     *
     * @param   mixed $columns column name or array($column, $alias) or object
     * @param   ...
     * @return  $this
     */
    public function select($columns = NULL)
    {
        $columns = func_get_args();
        $this->_is_array = true;
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'select',
            'args' => $columns,
        );

        return $this;
    }

    /**
     * Choose the columns to select from.
     *
     * @param   mixed $columns column name or array($column, $alias) or object
     * @return  $this
     */
    public function select_array($columns = NULL)
    {
        $this->_is_array = true;
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'select',
            'args' => $columns,
        );

        return $this;
    }

    /**
     * Choose the tables to select "FROM ..."
     *
     * @param   mixed $tables table name or array($table, $alias) or object
     * @param   ...
     * @return  $this
     */
    public function from($tables)
    {
        $tables = func_get_args();

        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'from',
            'args' => $tables,
        );

        return $this;
    }

    /**
     * Adds addition tables to "JOIN ...".
     *
     * @param   mixed $table column name or array($column, $alias) or object
     * @param   string $type join type (LEFT, RIGHT, INNER, etc)
     * @return  $this
     */
    public function join($table, $type = NULL)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'join',
            'args' => array($table, $type),
        );

        return $this;
    }

    /**
     * Adds "ON ..." conditions for the last created JOIN statement.
     *
     * @param   mixed $c1 column name or array($column, $alias) or object
     * @param   string $op logic operator
     * @param   mixed $c2 column name or array($column, $alias) or object
     * @return  $this
     */
    public function on($c1, $op, $c2)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'on',
            'args' => array($c1, $op, $c2),
        );

        return $this;
    }

    /**
     * Creates a "GROUP BY ..." filter.
     *
     * @param   mixed $columns column name or array($column, $alias) or object
     * @param   ...
     * @return  $this
     */
    public function group_by($columns)
    {
        $columns = func_get_args();

        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'group_by',
            'args' => $columns,
        );

        return $this;
    }

    /**
     * Alias of and_having()
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $op logic operator
     * @param   mixed $value column value
     * @return  $this
     */
    public function having($column, $op, $value = NULL)
    {
        return $this->and_having($column, $op, $value);
    }

    /**
     * Creates a new "AND HAVING" condition for the query.
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $op logic operator
     * @param   mixed $value column value
     * @return  $this
     */
    public function and_having($column, $op, $value = NULL)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'and_having',
            'args' => array($column, $op, $value),
        );

        return $this;
    }

    /**
     * Creates a new "OR HAVING" condition for the query.
     *
     * @param   mixed $column column name or array($column, $alias) or object
     * @param   string $op logic operator
     * @param   mixed $value column value
     * @return  $this
     */
    public function or_having($column, $op, $value = NULL)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'or_having',
            'args' => array($column, $op, $value),
        );

        return $this;
    }

    /**
     * Alias of and_having_open()
     *
     * @return  $this
     */
    public function having_open()
    {
        return $this->and_having_open();
    }

    /**
     * Opens a new "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function and_having_open()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'and_having_open',
            'args' => array(),
        );

        return $this;
    }

    /**
     * Opens a new "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function or_having_open()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'or_having_open',
            'args' => array(),
        );

        return $this;
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function having_close()
    {
        return $this->and_having_close();
    }

    /**
     * Closes an open "AND HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function and_having_close()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'and_having_close',
            'args' => array(),
        );

        return $this;
    }

    /**
     * Closes an open "OR HAVING (...)" grouping.
     *
     * @return  $this
     */
    public function or_having_close()
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'or_having_close',
            'args' => array(),
        );

        return $this;
    }

    /**
     * Start returning results after "OFFSET ..."
     *
     * @param   integer $number starting result number
     * @return  $this
     */
    public function offset($number)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'offset',
            'args' => array($number),
        );

        return $this;
    }

    /**
     * Enables the query to be cached for a specified amount of time.
     *
     * @param   integer $lifetime number of seconds to cache
     * @return  $this
     * @uses    \Delorius\Caching\Cache::EXPIRE_DEFAULT_TIME
     */
    public function cached($lifetime = NULL)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'cached',
            'args' => array($lifetime),
        );

        return $this;
    }

    /**
     * Set the value of a parameter in the query.
     *
     * @param   string $param parameter key to replace
     * @param   mixed $value value to use
     * @return  $this
     */
    public function param($param, $value)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'param',
            'args' => array($param, $value),
        );

        return $this;
    }

    /**
     * Adds "USING ..." conditions for the last created JOIN statement.
     *
     * @param   string $columns column name
     * @return  $this
     */
    public function using($columns)
    {
        // Add pending DataBase call which is executed after query type is determined
        $this->_db_pending[] = array(
            'name' => 'using',
            'args' => array($columns),
        );

        return $this;
    }

    /**
     * Checks whether a column value is unique.
     * Excludes itself if loaded.
     *
     * @param   string $field the field to check for uniqueness
     * @param   mixed $value the value to check for uniqueness
     * @return  bool     whteher the value is unique
     */
    public function unique($field, $value)
    {
        $model = self::model()
            ->where($field, '=', $value)
            ->find();

        if ($this->loaded()) {
            return (!($model->loaded() AND $model->pk() != $this->pk()));
        }

        return (!$model->loaded());
    }

    public function db_config()
    {
        return $this->_db_config;
    }


    protected function _init()
    {
        $this->_init_starter = true;
        $this->_object_name = strtolower(get_class($this));
        if (!$init = ORM::$_init_cache[$this->_object_name]) {
            if (!is_object($this->_db)) {
                $init['_db'] = DataBase::instance($this->_db_config);
            }
            if (empty($this->_table_name)) {
                throw new Error("Укажите названия таблицы: @class  = {$this->_object_name} ");
            }
            ORM::$_init_cache[$this->_object_name] = $init;
        }


        $this->_table_columns = Helpers::mergeColumns(
            $this->_table_columns,
            (array)ORM::$_update_table_columns[get_called_class()]
        );

        $this->attachBehaviors(Helpers::mergeBehaviors(
            $this->behaviors(),
            (array)ORM::$_update_behaviors[get_called_class()])
        );

        foreach ($init as $property => $value) {
            $this->{$property} = $value;
        }

    }

    /**
     * Initializes the DataBase Builder to given query type
     *
     * @param  integer $type Type of DataBase query
     * @return ORM
     */
    protected function _build($type)
    {
        // Construct new builder object based on query type
        switch ($type) {
            case DataBase::SELECT:
                $this->_db_builder = DB::select();
                break;
            case DataBase::UPDATE:
                $this->_db_builder = DB::update(array($this->_table_name, $this->_object_name));
                break;
            case DataBase::DELETE:
                // Cannot use an alias for DELETE queries
                $this->_db_builder = DB::delete($this->_table_name);
        }

        // Process pending DataBase method calls
        foreach ($this->_db_pending as $method) {
            $name = $method['name'];
            $args = $method['args'];

            $this->_db_applied[$name] = $name;

            call_user_func_array(array($this->_db_builder, $name), $args);
        }

        return $this;
    }

    /**
     * Returns an array of columns to include in the select query. This method
     * can be overridden to change the default select behavior.
     *
     * @return array Columns to select
     */
    protected function _build_select()
    {
        $columns = array();

        if (!$this->_is_array)
            foreach ($this->_table_columns as $column) {
                $columns[] = array($this->_table_name . '.' . $column['column_name'], $column['column_name']);
            }

        return $columns;
    }

    /**
     * Loads a DataBase result, either as a new record for this model, or as
     * an iterator for multiple rows.
     *
     * @chainable
     * @param  bool $multiple Return an iterator or load a single row
     * @return \Delorius\Core\ORM | \Delorius\DataBase\Result
     */
    protected function _load_result($multiple = FALSE)
    {
        $token = Profiler::start('ORM', $this->table_name() . ' - ' . ($multiple ? 'find_all' : 'find'));
        $this->onBeforeFind($this);
        $this->_db_builder->from($this->_table_name);

        if ($multiple === FALSE) {
            // Only fetch 1 record
            $this->_db_builder->limit(1);
        }

        // Select all columns by default
        if ($select = $this->_build_select())
            $this->_db_builder->select_array($select);

        if ($multiple === TRUE) {
            // Return DataBase iterator casting to this object type
            if ($this->_is_array) {
                $result = $this->_db_builder->as_assoc(get_class($this))->execute($this->_db);
            } else {
                $result = $this->_db_builder->as_object(get_class($this))->execute($this->_db);
            }
            $this->reset();
            if (isset($token)) {
                Profiler::stop($token);
            }
            $this->_log($token);

            return $result;
        } else {

            if ($this->_is_array) {
                $result = $this->_db_builder->as_assoc()->execute($this->_db);
                if ($result->count() === 1) {
                    return $result->current();
                } else {
                    return $select;
                }
            }

            // Load the result as an associative array
            $result = $this->_db_builder->as_assoc(get_class($this))->execute($this->_db);
            $this->reset();
            if ($result->count() === 1) {
                // Load object values
                $this->_load_values($result->current());
            } else {
                // Clear the object, nothing was found
                $this->clear();
            }
            if (isset($token)) {
                Profiler::stop($token);
            }
            $this->_log($token);

            return $this;
        }
    }

    protected function _log($token)
    {
        if ($this->_log) {
            $this->_log = false;
            if (isset($token)) {
                list($time, $memory) = Profiler::total($token);
                $time = number_format($time, 6) . ' sec'; //sec
                $memory = number_format($memory / 1024, 4) . ' kB'; //kB
                $profiler = _sf('time={0}; memory={1};', $time, $memory);
            }
            Environment::getContext()->getService('logger')->info(_sf('{0} sql={1};', $profiler, $this->last_query()), $this->table_name());
        }
    }


    /**
     * Loads an array of values into into the current object.
     *
     * @chainable
     * @param  array $values Values to load
     * @return ORM
     */
    protected function _load_values(array $values)
    {
        if (array_key_exists($this->_primary_key, $values)) {
            if ($values[$this->_primary_key] !== NULL) {
                $this->_loaded = $this->_valid = TRUE;
                $this->_primary_key_value = $values[$this->_primary_key];
            } else {
                $this->_loaded = $this->_valid = FALSE;
            }

            if ($values[$this->_config_key] !== NULL) {
                $this->_config_key_value = Strings::unescape_array(json_decode($values[$this->_config_key], true));
            }
        }

        if ($this->_loaded) {
            $this->_original_values = $this->_object = $values;
        }
        $this->onAfterFind($this);
        return $this;
    }

    public function isLoadIn($isUpdate = false)
    {
        $this->_load_values($this->_object);
        foreach (array_keys($this->_table_columns) as $key) {
            $this->_object[$key] = $this->_object[$key];
        }
        if (!$isUpdate)
            $this->_changed = array();
        return $this;
    }

    /**
     * Фиктивный объект
     * @param array $values
     * @return $this|ORM
     * @throws Error
     */
    public static function mock($values)
    {
        $orm = self::model(null, false);
        foreach ($values as $name => $value) {
            $orm->set($name, $value);
        }

        return $orm->isLoadIn();
    }


    /**
     * Rule definitions for validation
     *
     * @return array
     */
    protected function rules()
    {
        return array();
    }

    /**
     * Filter definitions for validation
     *
     * @return array
     */
    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            )
        );
    }

    /**
     * Label definitions for validation
     *
     * @return array
     */
    public function labels()
    {
        return array();
    }


    /**
     * Returns a list of behaviors that this model should behave as.
     * The return value should be an array of behavior configurations indexed by
     * behavior names. Each behavior configuration can be either a string specifying
     * the behavior class or an array of the following structure:
     * <pre>
     * 'behaviorName'=>array(
     *     'class'=>'\path\to\BehaviorClass',
     *     'property1'=>'value1',
     *     'property2'=>'value2',
     * )
     * or
     * behaviorName'=> '\path\to\BehaviorClass' ||  new \path\to\BehaviorClass()
     * </pre>
     *
     * Note, the behavior classes must implement {@link IBehavior} or extend from
     * {@link Behavior}. Behaviors declared in this method will be attached
     * to the model when it is instantiated.
     * @return array the behavior configurations (behavior name=>behavior configuration)
     */
    protected function behaviors()
    {
        return array();
    }

    /**
     * Filters a value for a specific column
     *
     * @param  string $field The column name
     * @param  string $value The value to filter
     * @return string
     */
    protected function run_filter($field, $value)
    {
        $filters = $this->filters();

        // Get the filters for this column
        $wildcards = empty($filters[TRUE]) ? array() : $filters[TRUE];

        // Merge in the wildcards
        $filters = empty($filters[$field]) ? $wildcards : array_merge($wildcards, $filters[$field]);

        // Bind the field name and model so they can be used in the filter method
        $_bound = array
        (
            ':field' => $field,
            ':model' => $this,
        );

        foreach ($filters as $array) {
            // Value needs to be bound inside the loop so we are always using the
            // version that was modified by the filters that already ran
            $_bound[':value'] = $value;

            // Filters are defined as array($filter, $params)
            $filter = $array[0];
            $params = Arrays::get($array, 1, array(':value'));

            foreach ($params as $key => $param) {
                if (is_string($param) AND array_key_exists($param, $_bound)) {
                    // Replace with bound value
                    $params[$key] = $_bound[$param];
                }
            }

            if (is_array($filter) OR !is_string($filter)) {
                // This is either a callback as an array or a lambda
                $value = call_user_func_array($filter, $params);
            } elseif (strpos($filter, '::') === FALSE) {
                // Use a function call
                $function = new \ReflectionFunction($filter);
                // Call $function($this[$field], $param, ...) with Reflection
                $value = $function->invokeArgs($params);
            } else {
                // Split the class and method of the rule
                list($class, $method) = explode('::', $filter, 2);

                // Use a static method call
                $method = new \ReflectionMethod($class, $method);

                // Call $Class::$method($this[$field], $param, ...) with Reflection
                $value = $method->invokeArgs(NULL, $params);
            }
        }

        return $value;
    }


    protected function _marge_columns_set()
    {
        foreach ($this->_table_columns_set as $column) {
            $this->{$column} = $this->{$column};
        }
    }


    /*
     * @throws OrmValidationError
     */
    protected function validException()
    {
        $exception = new OrmValidationError($this->getErrors(), $this->getErrorFields());
        $exception->add_object_name($this->object_name());
        throw $exception;
    }


    /*
     *  magic method
     */


    /**
     * Handles retrieval of all model values, relationships, and metadata.
     * [!!] This should not be overridden.
     *
     * @param   string $column Column name
     * @return  mixed
     */
    public function &__get($column)
    {
        if (isset($this->_object[$column]) || isset($this->_table_columns[$column])) {
            return $this->get($column);
        }

        return parent::__get($column);
    }

    /**
     * Base set method.
     * [!!] This should not be overridden.
     *
     * @param  string $column Column name
     * @param  mixed $value Column value
     * @return void
     */
    public function __set($column, $value)
    {
        if (!$this->_init_starter) {
            $this->_init();
        }

        if (isset($this->_object[$column]) || isset($this->_table_columns[$column])) {
            return $this->set($column, $value);
        }

        parent::__set($column, $value);
    }

    /**
     * Checks if object data is set.
     *
     * @param  string $column Column name
     * @return boolean
     */
    public function __isset($column)
    {

        return (isset($this->_object[$column]) || isset($this->_table_columns[$column]));
    }

    /**
     * Unsets object data.
     *
     * @param  string $column Column name
     * @return void
     */
    public function __unset($column)
    {
        if ($this->__isset($column)) {
            unset($this->_object[$column], $this->_changed[$column]);
        }

        if (parent::__isset($column))
            parent::__unset($column);

    }

    /**
     * Displays the primary key of a model when it is converted to a string.
     *
     * @return string
     */
    public function __toString()
    {
        return (string)$this->pk();
    }


    /********Iterator  ******/

    protected $_count_fields;

    /**
     * Return the current element
     * @link http://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     */
    public function current()
    {
        $key = $this->key();
        return $this->get($key);
    }

    /**
     * Move forward to next element
     * @link http://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     */
    public function next()
    {
        $this->_count_fields++;
    }

    /**
     * Return the key of the current element
     * @link http://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     */
    public function key()
    {
        reset($this->_object);
        for ($i = 0; $i < $this->_count_fields; $i++) {
            next($this->_object);
        }
        return key($this->_object);
    }

    /**
     * @link http://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     */
    public function valid()
    {
        $key = $this->key();
        return isset($this->_object[$key]);
    }

    /**
     * @link http://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     */
    public function rewind()
    {
        $this->_count_fields = 0;
    }

    /**
     * unserialize object
     */
    public function __wakeup()
    {
        $this->_init();
        $this->attachBehaviors($this->behaviors());
        $this->init();
    }
}