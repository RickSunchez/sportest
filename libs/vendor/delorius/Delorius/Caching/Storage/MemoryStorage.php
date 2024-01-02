<?php
namespace Delorius\Caching\Storage;

use Delorius\Caching\Cache;
use Delorius\Caching\IStorage;
use Delorius\Core\Object;

/**
 * Memory cache storage.
 */
class MemoryStorage extends Object implements IStorage
{
	/** @var array */
	private $data = array();


	/**
	 * Read from cache.
	 * @param  string key
	 * @return mixed|NULL
	 */
	public function read($key)
	{
		return isset($this->data[$key]) ? $this->data[$key] : NULL;
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
	public function write($key, $data, array $dependencies)
	{
		$this->data[$key] = $data;
	}


	/**
	 * Removes item from the cache.
	 * @param  string key
	 * @return void
	 */
	public function remove($key)
	{
		unset($this->data[$key]);
	}


	/**
	 * Removes items from the cache by conditions & garbage collector.
	 * @param  array  conditions
	 * @return void
	 */
	public function clean(array $conditions = null)
	{
		if (!empty($conditions[Cache::ALL])) {
			$this->data = array();
		}
	}

}
