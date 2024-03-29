<?php
namespace Delorius\Security;

/**
 * Interface for persistent storage for user object data.
 *
 * @author David Grudl, Jan Tichý
 */
interface IUserStorage
{
	/** Log-out reason {@link IUserStorage::getLogoutReason()} */
	const MANUAL = 1,
		INACTIVITY = 2,
		BROWSER_CLOSED = 4;

	/** Log-out behavior */
	const CLEAR_IDENTITY = 8;

	/**
	 * Sets the authenticated status of this user.
	 * @param  bool
	 * @return void
	 */
	function setAuthenticated($state);

	/**
	 * Is this user authenticated?
	 * @return bool
	 */
	function isAuthenticated();

	/**
	 * Sets the user identity.
	 * @return void
	 */
	function setIdentity(IIdentity $identity = NULL);

	/**
	 * Returns current user identity, if any.
	 * @return \Delorius\Security\IIdentity|NULL
	 */
	function getIdentity();

	/**
	 * Enables log out from the persistent storage after inactivity.
	 * @param  string|int|DateTime number of seconds or timestamp
	 * @param  int Log out when the browser is closed | Clear the identity from persistent storage?
	 * @return void
	 */
	function setExpiration($time, $flags = 0);

	/**
	 * Why was user logged out?
	 * @return int
	 */
	function getLogoutReason();

}
