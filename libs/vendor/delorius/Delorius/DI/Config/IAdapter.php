<?php
namespace Delorius\DI\Config;


/**
 * Adapter for reading and writing configuration files.
 */
interface IAdapter
{

	/**
	 * Reads configuration from file.
	 * @param  string  file name
	 * @return array
	 */
	function load($file);

	/**
	 * Generates configuration string.
	 * @return string
	 */
	function dump(array $data);

}
