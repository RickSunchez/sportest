<?php
namespace Delorius\ComponentModel;
/**
 * Containers are objects that logically contain zero or more IComponent components.
 *
 *
 */
interface IContainer extends IComponent
{

	/**
	 * Adds the specified component to the IContainer.
	 * @param  IComponent
	 * @param  string
	 * @return void
	 */
	function addComponent(IComponent $component, $name);

	/**
	 * Removes a component from the IContainer.
	 * @param  IComponent
	 * @return void
	 */
	function removeComponent(IComponent $component);

	/**
	 * Returns single component.
	 * @param  string
	 * @return IComponent|NULL
	 */
	function getComponent($name);

	/**
	 * Iterates over a components.
	 * @param  bool    recursive?
	 * @param  string  class types filter
	 * @return \Iterator
	 */
	function getComponents($deep = FALSE, $filterType = NULL);

}
