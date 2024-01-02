<?php
namespace Delorius\DataBase;

/**
 * DataBase query wrapper.  See [Parameterized Statements](DataBase/query/parameterized) for usage and examples.
 *
 * @package    Kohana/DataBase
 * @category   Query
 * @author     Kohana Team
 * @copyright  (c) 2008-2009 Kohana Team
 * @license    http://kohanaphp.com/license
 */

use Delorius\Caching\Cache;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\DataBase\Result\Cached;
use Delorius\Utils\Strings;

class Query
{

    // key for cached
    protected $_cache_key;

    /** @var \Delorius\Caching\Cache */
    protected $_cache;

    // Query type
    protected $_type;

    // Execute the query during a cache hit
    protected $_force_execute = FALSE;

    // Cache lifetime
    protected $_lifetime = NULL;

    // SQL statement
    protected $_sql;

    // Quoted query parameters
    protected $_parameters = array();

    // Return results as associative arrays or objects
    protected $_as_object = FALSE;

    // Parameters for __construct when using object results
    protected $_object_params = array();


    /**
     * Creates a new SQL query of the specified type.
     *
     * @param   integer $type query type: DataBase::SELECT, DataBase::INSERT, etc
     * @param   string $sql query string
     * @return  void
     */
    public function __construct($type, $sql)
    {
        $this->_type = $type;
        $this->_sql = $sql;
        $this->_cache = Environment::getContext()->getService('cache')->derive('db');
    }

    /**
     * Return the SQL query string.
     *
     * @return  string
     */
    public function __toString()
    {
        try {
            // Return the SQL string
            return $this->compile(DataBase::instance());
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * Get the type of the query.
     *
     * @return  integer
     */
    public function type()
    {
        return $this->_type;
    }

    /**
     * Enables the query to be cached for a specified amount of time.
     *
     * @param   integer $lifetime number of seconds to cache, 0 deletes it from the cache
     * @param   boolean  whether or not to execute the query during a cache hit
     * @return  $this
     */
    public function cached($lifetime = NULL, $force = FALSE)
    {
        if ($lifetime === NULL) {
            // Use the global setting
            $lifetime = Cache::EXPIRE_DEFAULT_TIME;
        }

        $this->_force_execute = $force;
        $this->_lifetime = $lifetime;

        return $this;
    }

    /**
     * Returns results as associative arrays
     *
     * @return  $this
     */
    public function as_assoc($class = TRUE)
    {
        $this->_cache_key = $class;
        $this->_as_object = FALSE;
        $this->_object_params = array();
        return $this;
    }

    /**
     * Returns results as objects
     *
     * @param   string $class classname or TRUE for stdClass
     * @param   array $params
     * @return  $this
     */
    public function as_object($class = TRUE, array $params = NULL)
    {
        $this->_cache_key = $this->_as_object = $class;

        if ($params) {
            // Add object parameters
            $this->_object_params = $params;
        }

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
        // Add or overload a new parameter
        $this->_parameters[$param] = $value;

        return $this;
    }

    /**
     * Bind a variable to a parameter in the query.
     *
     * @param   string $param parameter key to replace
     * @param   mixed $var variable to use
     * @return  $this
     */
    public function bind($param, & $var)
    {
        // Bind a value to a variable
        $this->_parameters[$param] =& $var;

        return $this;
    }

    /**
     * Add multiple parameters to the query.
     *
     * @param   array $params list of parameters
     * @return  $this
     */
    public function parameters(array $params)
    {
        // Merge the new parameters in
        $this->_parameters = $params + $this->_parameters;

        return $this;
    }

    /**
     * Compile the SQL query and return it. Replaces any parameters with their
     * given values.
     *
     * @param   mixed $db DataBase instance or name of instance
     * @return  string
     */
    public function compile($db = NULL)
    {
        if (!is_object($db)) {
            // Get the DataBase instance
            $db = DataBase::instance($db);
        }

        // Import the SQL locally
        $sql = $this->_sql;

        if (!empty($this->_parameters)) {
            // Quote all of the values
            $values = array_map(array($db, 'quote'), $this->_parameters);

            // Replace the values in the SQL
            $sql = strtr($sql, $values);
        }

        return $sql;
    }

    /**
     * Execute the current query on the given DataBase.
     *
     * @param   mixed $db DataBase instance or name of instance
     * @param   string   result object classname, TRUE for stdClass or FALSE for array
     * @param   array    result object constructor arguments
     * @return  object   DataBase\Result for SELECT queries
     * @return  mixed    the insert id for INSERT queries
     * @return  integer  number of affected rows for all other queries
     */
    public function execute($db = NULL, $as_object = NULL, $object_params = NULL)
    {
        if (!is_object($db)) {
            # Get the DataBase instance
            $db = DataBase::instance($db);
        }

        if ($as_object === NULL) {
            $as_object = $this->_as_object;
            $this->_cache = Environment::getContext()
                ->getService('cache')
                ->derive('orm' . substr(Strings::cached_rate_id($this->_as_object), 0, 10));
        }

        if ($object_params === NULL) {
            $object_params = $this->_object_params;
        }

        # Compile the SQL query
        $sql = $this->compile($db);
        $cache_key = $this->generatorKey($this->_cache_key, $sql);
        if ($this->_lifetime !== NULL AND $this->_type === DataBase::SELECT) {
            # Set the cache key based on the DataBase instance name and SQL
            if ($this->_lifetime == 0) {
                $this->_cache->remove($cache_key);
            } else if (
                ($result = $this->_cache->load($cache_key)) !== NULL &&
                !$this->_force_execute
            ) {
                # Return a cached result
                return new Cached($result, $sql, $as_object, $object_params);
            }
        }

        # Execute the query
        $result = $db->query($this->_type, $sql, $as_object, $object_params);

        if (
            isset($cache_key) &&
            $this->_lifetime !== NULL &&
            $this->_lifetime != 0
        ) {
            # Cache the result array
            $dp = array();
            $dp[Cache::EXPIRE] = $this->_lifetime;
            $dp[Cache::TAGS][] = Strings::lower($this->_cache_key);
            if (class_exists($this->_as_object)) {
                $class_name = $this->_as_object;
                $class = new ${class_name};
                if ($class instanceof ORM) {
                    $dp[Cache::TAGS][] = $class->table_name();
                }
            }
            $this->_cache->save($cache_key, $result->as_array(), $dp);
        }

        return $result;
    }

    /**
     * @param $key
     * @param $sql
     * @return string
     */
    protected function generatorKey($key, $sql)
    {
        return _sf('object_{0}:db:sql_{1}', md5(strtolower($key)), md5($sql));
    }

}
