<?php
namespace Delorius\Caching;

use Delorius\Core\DateTime;
use Delorius\Core\Object;
use Delorius\Tools\Debug\Profiler;
use Delorius\Utils\Callback;

Class Cache extends Object implements \ArrayAccess
{
    /** dependency */
    const PRIORITY = 'priority',
        EXPIRATION = 'expire',
        EXPIRE = 'expire',
        SLIDING = 'sliding',
        TAGS = 'tags',
        FILES = 'files',
        ITEMS = 'items',
        CONSTS = 'consts',
        CALLBACKS = 'callbacks',
        ALL = 'all';

    const EXPIRE_DEFAULT_TIME = '+1 days';

    /** @internal */
    const NAMESPACE_SEPARATOR = "\x00";


    /** @var IStorage */
    private $storage;

    /** @var string */
    private $namespace;


    /** @var string  last query cache used by offsetGet() */
    private $key;

    /** @var mixed  last query cache used by offsetGet()  */
    private $data;


    public function __construct(IStorage $storage, $namespace = NULL)
    {
        $this->storage = $storage;
        $this->namespace = $namespace . self::NAMESPACE_SEPARATOR;
    }

    /**
     * Returns cache storage.
     * @return IStorage
     */
    public function getStorage()
    {
        return $this->storage;
    }

    /**
     * Returns cache namespace.
     * @return string
     */
    public function getNamespace()
    {
        return (string) substr($this->namespace, 0, -1);
    }

    /**
     * Returns new nested cache object.
     * @param  string
     * @return Cache
     */
    public function derive($namespace)
    {
        $derived = new static($this->storage, $this->namespace . $namespace);
        return $derived;
    }

    /**
     * Reads the specified item from the cache or generate it.
     * @param  mixed $key
     * @param  callable
     * @return mixed|NULL
     */
    public function load($key, $fallback = NULL)
    {
        $token = Profiler::start('Cache',$key);
        $data = $this->storage->read($this->generateKey($key));
        if ($data === NULL && $fallback) {
            Profiler::stop($token);
            return $this->save($key, Callback::closure($fallback));
        }
        Profiler::stop($token);
        return $data;
    }

    /**
     * Writes item into the cache.
     * Dependencies are:
     * - Cache::PRIORITY => (int) priority
     * - Cache::EXPIRATION => (timestamp) expiration
     * - Cache::SLIDING => (bool) use sliding expiration?
     * - Cache::TAGS => (array) tags
     * - Cache::FILES => (array|string) file names
     * - Cache::ITEMS => (array|string) cache items
     * - Cache::CONSTS => (array|string) cache items
     *
     * @param  mixed  $key
     * @param  mixed  $value
     * @param  array  $dependencies
     * @return mixed  value itself
     * @throws Error
     */
    public function save($key, $data, array $dp = null)
    {
        $this->release();
        $key = $this->generateKey($key);

        if ($data instanceof Callback || $data instanceof \Closure) {
            $this->storage->lock($key);
            $data = call_user_func_array($data, array(& $dp));
        }

        if ($data === NULL) {
            $this->storage->remove($key);
        } else {
            $this->storage->write($key, $data, $this->completeDependencies($dp, $data));
            return $data;
        }
    }

    private function completeDependencies($dp, $data)
    {
        // convert expire into relative amount of seconds
        if (isset($dp[Cache::EXPIRATION])) {
            $dp[Cache::EXPIRATION] = DateTime::from($dp[Cache::EXPIRATION])->format('U') - time();
        }

        // convert FILES into CALLBACKS
        if (isset($dp[self::FILES])) {
            //clearstatcache();
            foreach (array_unique((array) $dp[self::FILES]) as $item) {
                $dp[self::CALLBACKS][] = array(array(__CLASS__, 'checkFile'), $item, @filemtime($item)); // @ - stat may fail
            }
            unset($dp[self::FILES]);
        }

        // add namespaces to items
        if (isset($dp[self::ITEMS])) {
            $dp[self::ITEMS] = array_unique(array_map(array($this, 'generateKey'), (array) $dp[self::ITEMS]));
        }

        // convert CONSTS into CALLBACKS
        if (isset($dp[self::CONSTS])) {
            foreach (array_unique((array) $dp[self::CONSTS]) as $item) {
                $dp[self::CALLBACKS][] = array(array(__CLASS__, 'checkConst'), $item, constant($item));
            }
            unset($dp[self::CONSTS]);
        }

        if (!is_array($dp)) {
            $dp = array();
        }
        return $dp;
    }

    /**
     * Removes item from the cache.
     * @param  mixed  $key
     * @return void
     */
    public function remove($key)
    {
        $this->save($key, NULL);
    }

    /**
     * Removes items from the cache by conditions.
     * Conditions are:
     * - Cache::PRIORITY => (int) priority
     * - Cache::TAGS => (array) tags
     * - Cache::ALL => TRUE
     * @return void
     */
    public function clean(array $conditions = NULL)
    {
        $this->release();
        $this->storage->clean((array) $conditions);
    }


    /**
     * Caches results of function/method calls.
     * @param  mixed
     * @return mixed
     */
    public function call($function)
    {
        $key = func_get_args();
        $key[0] = Callback::toReflection($function);
        return $this->load($key, function() use ($function, $key) {
            return Callback::invokeArgs($function, array_slice($key, 1));
        });
    }

    /**
     * Caches results of function/method calls.
     * @param  mixed
     * @param  array  $dependencies
     * @return \Closure
     */
    public function wrap($function, array $dependencies = NULL)
    {
        $cache = $this;
        return function() use ($cache, $function, $dependencies) {
            $key = array(Callback::toReflection($function), func_get_args());
            $data = $cache->load($key);
            if ($data === NULL) {
                $data = $cache->save($key, Callback::invokeArgs($function, $key[1]), $dependencies);
            }
            return $data;
        };
    }

    /**
     * Starts the output cache.
     * @param  mixed  $key
     * @return Output|NULL
     */
    public function start($key)
    {
        $data = $this->load($key);
        if ($data === NULL) {
            return new Output($this, $key);
        }
        echo $data;
    }


    /**
     * Generates internal cache key.
     *
     * @param  string
     * @return string
     */
    protected function generateKey($key)
    {
        return $this->namespace . md5(is_scalar($key) ? $key : serialize($key));
    }


    /********************* interface ArrayAccess ********************/

    /**
     * Inserts (replaces) item into the cache (\ArrayAccess implementation).
     * @param  mixed $key
     * @param  mixed
     * @return void
     * @throws Error
     */
    public function offsetSet($key, $data)
    {
        $this->save($key, $data);
    }


    /**
     * Retrieves the specified item from the cache or NULL if the key is not found (\ArrayAccess implementation).
     * @param  mixed $key
     * @return mixed|NULL
     * @throws Error
     */
    public function offsetGet($key)
    {
        $key = is_scalar($key) ? (string) $key : serialize($key);
        if ($this->key !== $key) {
            $this->key = $key;
            $this->data = $this->load($key);
        }
        return $this->data;
    }


    /**
     * Exists item in cache? (\ArrayAccess implementation).
     * @param  mixed $key
     * @return bool
     * @throws Error
     */
    public function offsetExists($key)
    {
        $this->release();
        return $this->offsetGet($key) !== NULL;
    }


    /**
     * Removes the specified item from the cache.
     * @param  mixed $key
     * @return void
     * @throws Error
     */
    public function offsetUnset($key)
    {
        $this->save($key, NULL);
    }



    /**
     * Discards the internal cache used by ArrayAccess.
     * @return void
     */
    public function release()
    {
        $this->key = $this->data = NULL;
    }

    /********************* dependency checkers *********************/


    /**
     * Checks CALLBACKS dependencies.
     * @param  array
     * @return bool
     */
    public static function checkCallbacks($callbacks)
    {
        foreach ($callbacks as $callback) {
            if (!call_user_func_array(array_shift($callback), $callback)) {
                return FALSE;
            }
        }
        return TRUE;
    }


    /**
     * Checks CONSTS dependency.
     * @param  string
     * @param  mixed
     * @return bool
     */
    private static function checkConst($const, $value)
    {
        return defined($const) && constant($const) === $value;
    }


    /**
     * Checks FILES dependency.
     * @param  string
     * @param  int
     * @return bool
     */
    private static function checkFile($file, $time)
    {
        return @filemtime($file) == $time; // @ - stat may fail
    }



}
