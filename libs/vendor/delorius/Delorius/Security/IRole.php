<?php
namespace Delorius\Security;

/**
 * Represents role, an object that may request access to an IResource.
 */
interface IRole
{

	/**
	 * Returns a string identifier of the Role.
	 * @return string
	 */
	function getRoleId();

}
