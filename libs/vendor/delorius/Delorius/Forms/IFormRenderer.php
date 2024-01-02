<?php
namespace Delorius\Forms;

/**
 * Defines method that must implement form renderer.
 *
 *
 */
interface IFormRenderer
{

	/**
	 * Provides complete form rendering.
	 * @return string
	 */
	function render(Form $form);

}
