<?php
namespace Delorius\Caching;

use Delorius\Core\Object;
use Delorius\Exception\Error;

/**
 * Output caching helper.
 *
 */
class Output extends Object
{
	/** @var array */
	public $dependencies;

	/** @var Cache */
	private $cache;

	/** @var string */
	private $key;



	public function __construct(Cache $cache, $key)
	{
		$this->cache = $cache;
		$this->key = $key;
		ob_start();
	}



	/**
	 * Stops and saves the cache.
	 * @param  array  dependencies
	 * @return void
	 */
	public function end(array $dp = NULL)
	{
		if ($this->cache === NULL) {
            throw new Error('Output cache has already been saved.');
		}
		$this->cache->save($this->key, ob_get_flush(), (array) $dp + (array) $this->dependencies);
		$this->cache = NULL;
	}

}
