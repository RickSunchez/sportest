<?php
namespace Delorius\Security;

use Delorius\Core\Object;

/**
 * Trivial implementation of IAuthenticator.
 *
 * @author     David Grudl
 */
class SimpleAuthenticator extends Object implements IAuthenticator
{
	/** @var array */
	private $userlist;


	/**
	 * @param  array  list of pairs username => password
	 */
	public function __construct(array $userlist)
	{
		$this->userlist = $userlist;
	}



	/**
	 * Performs an authentication against e.g. DataBase.
	 * and returns IIdentity on success or throws AuthenticationException
	 * @return IIdentity
	 * @throws AuthenticationException
	 */
	public function authenticate(array $credentials)
	{
		list($username, $password) = $credentials;
		foreach ($this->userlist as $name => $pass) {
			if (strcasecmp($name, $username) === 0) {
				if ((string) $pass === (string) $password) {
					return new Identity($name);
				} else {
					throw new AuthenticationException("Invalid password.", self::INVALID_CREDENTIAL);
				}
			}
		}
		throw new AuthenticationException("User '$username' not found.", self::IDENTITY_NOT_FOUND);
	}

}
