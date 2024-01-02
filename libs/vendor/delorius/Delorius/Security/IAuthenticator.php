<?php
namespace Delorius\Security;

/**
 * Performs authentication.
 */
interface IAuthenticator
{
	/** Credential key */
	const USERNAME = 0,
		PASSWORD = 1;

	/** Exception error code */
	const IDENTITY_NOT_FOUND = 1,
		INVALID_CREDENTIAL = 2,
		FAILURE = 3,
		NOT_APPROVED = 4;

	/**
	 * Performs an authentication against e.g. DataBase.
	 * and returns IIdentity on success or throws AuthenticationException
	 * @param  array
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	function authenticate(array $credentials);

}
