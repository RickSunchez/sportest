<?php
namespace Delorius\DI\Config\Adapters;

use Delorius\Core\Object;
use Delorius\DI\Config\IAdapter;

/**
 * Reading and generating PHP files.
 */
class PhpAdapter extends Object implements IAdapter
{

	/**
	 * Reads configuration from PHP file.
	 * @param  string  file name
	 * @return array
	 */
	public function load($file)
	{
		return require $file;
	}


	/**
	 * Generates configuration in PHP format.
	 * @return string
	 */
	public function dump(array $data)
	{
		return "<?php // generated by Delorius \nreturn " . \Delorius\PhpGenerator\Helpers::dump($data) . ';';
	}

}
