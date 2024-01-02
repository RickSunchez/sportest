<?php
namespace Delorius\Page;

use Delorius\Exception\Error;


/**
 * Container for menu controls.
 */
class Container extends \Delorius\ComponentModel\Container implements \ArrayAccess
{



	/********************* interface \ArrayAccess ****************d*g**/

	/**
	 * Adds the component to the container.
	 * @param  string  component name
	 * @param  \Delorius\ComponentModel\IComponent
	 * @return void
	 */
	final public function offsetSet($name, $component)
	{
		$this->addComponent($component, $name);
	}



	/**
	 * Returns component specified by name. Throws exception if component doesn't exist.
	 * @param  string  component name
	 * @return \Delorius\ComponentModel\IComponent
	 * @throws Error
	 */
	final public function offsetGet($name)
	{
		return $this->getComponent($name, TRUE);
	}



	/**
	 * Does component specified by name exists?
	 * @param  string  component name
	 * @return bool
	 */
	final public function offsetExists($name)
	{
		return $this->getComponent($name, FALSE) !== NULL;
	}



	/**
	 * Removes component from the container.
	 * @param  string  component name
	 * @return void
	 */
	final public function offsetUnset($name)
	{
		$component = $this->getComponent($name, FALSE);
		if ($component !== NULL) {
			$this->removeComponent($component);
		}
	}



	/**
	 * Prevents cloning.
	 */
	final public function __clone()
	{
		throw new Error('Form cloning is not supported yet.');
	}
}
