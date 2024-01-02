<?php

namespace Delorius\Caching;
/**
 * Cache storage.
 *
 */
interface IStorage
{

	/**
	 * Read from cache.
	 * @param  string key
	 * @return mixed
	 */
	function read($key);

	/**
	 * Prevents item reading and writing. Lock is released by write() or remove().
	 * @param  string key
	 * @return mixed
	 */
	function lock($key);

	/**
	 * Writes item into the cache.
	 * @param  string $key
	 * @param  mixed  $data
	 * @param  array  $dependencies
	 * @return mixed
	 */
	function write($key, $data, array $dependencies);

	/**
	 * Removes item from the cache.
	 * @param  string $key
	 * @return mixed
	 */
	function remove($key);

	/**
	 * Removes items from the cache by conditions.
	 * @param  array  $conditions  - conditions
	 * @return mixed
	 */
	function clean(array $conditions = null);

}
