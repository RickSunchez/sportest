<?php
namespace Delorius\Caching\Storage;

use Delorius\Caching\Cache;
use Delorius\Caching\IStorage;
use Delorius\Core\Object;
use Delorius\Exception\Error;

/**
 * Memcached storage.
 *
 * @author     David Grudl
 */
class MemcachedStorage extends Object implements IStorage
{
	/** @internal cache structure */
	const META_CALLBACKS = 'callbacks',
		META_DATA = 'data',
		META_DELTA = 'delta';

	/** @var \Memcache */
	private $memcache;

	/** @var string */
	private $prefix;

	/** @var IJournal */
	private $journal;


	/**
	 * Checks if Memcached extension is available.
	 * @return bool
	 */
	public static function isAvailable()
	{
		return extension_loaded('memcache');
	}


	public function __construct($host = 'localhost', $port = 11211, $prefix = '', IJournal $journal = NULL)
	{
		if (!static::isAvailable()) {
			throw new Error("PHP extension 'memcache' is not loaded.");
		}

		$this->prefix = $prefix;
		$this->journal = $journal;
		$this->memcache = new \Memcache;
		if ($host) {
			$this->addServer($host, $port);
		}
	}


	public function addServer($host = 'localhost', $port = 11211, $timeout = 1)
	{
		if ($this->memcache->addServer($host, $port, TRUE, 1, $timeout) === FALSE) {
			$error = error_get_last();
			throw new Error("Memcache::addServer(): $error[message].");
		}
	}


	/**
	 * @return \Memcache
	 */
	public function getConnection()
	{
		return $this->memcache;
	}


	/**
	 * Read from cache.
	 * @param  string key
	 * @return mixed|NULL
	 */
	public function read($key)
	{
		$key = $this->prefix . $key;
		$meta = $this->memcache->get($key);
		if (!$meta) {
			return NULL;
		}

		// meta structure:
		// array(
		//     data => stored data
		//     delta => relative (sliding) expiration
		//     callbacks => array of callbacks (function, args)
		// )

		// verify dependencies
		if (!empty($meta[self::META_CALLBACKS]) && !Cache::checkCallbacks($meta[self::META_CALLBACKS])) {
			$this->memcache->delete($key, 0);
			return NULL;
		}

		if (!empty($meta[self::META_DELTA])) {
			$this->memcache->replace($key, $meta, 0, $meta[self::META_DELTA] + time());
		}

		return $meta[self::META_DATA];
	}


	/**
	 * Prevents item reading and writing. Lock is released by write() or remove().
	 * @param  string key
	 * @return void
	 */
	public function lock($key)
	{
	}


	/**
	 * Writes item into the cache.
	 * @param  string key
	 * @param  mixed  data
	 * @param  array  dependencies
	 * @return void
	 */
	public function write($key, $data, array $dp)
	{
		if (isset($dp[Cache::ITEMS])) {
			throw new Error('Dependent items are not supported by MemcachedStorage.');
		}

		$key = $this->prefix . $key;
		$meta = array(
			self::META_DATA => $data,
		);

		$expire = 0;
		if (isset($dp[Cache::EXPIRATION])) {
			$expire = (int) $dp[Cache::EXPIRATION];
			if (!empty($dp[Cache::SLIDING])) {
				$meta[self::META_DELTA] = $expire; // sliding time
			}
		}

		if (isset($dp[Cache::CALLBACKS])) {
			$meta[self::META_CALLBACKS] = $dp[Cache::CALLBACKS];
		}

		if (isset($dp[Cache::TAGS]) || isset($dp[Cache::PRIORITY])) {
			if (!$this->journal) {
				throw new Error('CacheJournal has not been provided.');
			}
			$this->journal->write($key, $dp);
		}

		$this->memcache->set($key, $meta, 0, $expire);
	}


	/**
	 * Removes item from the cache.
	 * @param  string key
	 * @return void
	 */
	public function remove($key)
	{
		$this->memcache->delete($this->prefix . $key, 0);
	}


	/**
	 * Removes items from the cache by conditions & garbage collector.
	 * @param  array  conditions
	 * @return void
	 */
	public function clean(array $conditions = null)
	{
		if (!empty($conditions[Cache::ALL]) || $conditions == null) {
			$this->memcache->flush();

		} elseif ($this->journal) {
			foreach ($this->journal->clean($conditions) as $entry) {
				$this->memcache->delete($entry, 0);
			}
		}
	}

}
