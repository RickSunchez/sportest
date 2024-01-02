<?php
namespace Delorius\Forms;


/**
 * Defines method that must be implemented to allow a control to submit web form.
 *
 *
 */
interface ISubmitterControl extends IControl
{

	/**
	 * Tells if the form was submitted by this button.
	 * @return bool
	 */
	function isSubmittedBy();

	/**
	 * Gets the validation scope. Clicking the button validates only the controls within the specified scope.
	 * @return mixed
	 */
	function getValidationScope();

}
