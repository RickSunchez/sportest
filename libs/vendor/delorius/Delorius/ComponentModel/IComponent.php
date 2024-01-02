<?php
namespace Delorius\ComponentModel;

/**
 * Provides functionality required by all components.
 *
 *
 */
interface IComponent
{
	/** Separator for component names in path concatenation. */
	const NAME_SEPARATOR = '-';

	/**
	 * @return string
	 */
	function getName();

	/**
	 * Returns the container if any.
	 * @return IContainer|NULL
	 */
	function getParent();

	/**
	 * Sets the parent of this component.
	 * @param  IContainer
	 * @param  string
	 * @return void
	 */
	function setParent(IContainer $parent = NULL, $name = NULL);

}
