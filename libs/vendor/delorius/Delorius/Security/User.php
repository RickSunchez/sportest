<?php

namespace Delorius\Security;

use Delorius\Core\Object;
use Delorius\Exception\Error;
use Delorius\Http\Storage\UserStorage;
use Delorius\Utils\Arrays;

Class User extends Object {
    /** @deprecated */
    const MANUAL = IUserStorage::MANUAL,
            INACTIVITY = IUserStorage::INACTIVITY,
            BROWSER_CLOSED = IUserStorage::BROWSER_CLOSED;

    const DEFAULT_NAMESPACE = 'user';

    /** @var string  default role for unauthenticated user */
    public $guestRole = 'guest';

    /** @var string  default role for authenticated user without own identity */
    public $authenticatedRole = 'registered';

    /** @var string role of root system */
    public $rootRole = 'root';

    /** @var array of function(User $sender); Occurs when the user is successfully logged in */
    public $onLoggedIn;

    /** @var array of function(User $sender); Occurs when the user is logged out */
    public $onLoggedOut;

    /** @var IUserStorage Session storage for current user */
    private $storage;

    /** @var IAuthenticator */
    private $authenticator;

    /** @var IAuthorizator */
    private $authorizator;


    public function __construct(
        IUserStorage $storage,
        IAuthenticator $authenticator = NULL,
        IAuthorizator $authorizator = NULL
    )
    {
        $this->storage = $storage;
        $this->authenticator = $authenticator;
        $this->authorizator = $authorizator;
    }

    /**
     * @return UserStorage  
     */
    final public function getStorage() {
        return $this->storage;
    }

    /** ******************* Authentication ****************** */

    /**
     * Conducts the authentication process. Parameters are optional.
     * @param  mixed optional parameter (e.g. username or IIdentity)
     * @param  mixed optional parameter (e.g. password)
     * @return void
     * @throws AuthenticationException if authentication was not successful
     */
    public function login($id = NULL, $password = NULL)
    {
        $this->logout(TRUE);
        if (!$id instanceof IIdentity) {
            $id = $this->getAuthenticator()->authenticate(func_get_args());
        }
        $this->storage->setIdentity($id);
        $this->storage->setAuthenticated(TRUE);
        $this->onLoggedIn($this);
    }

    /**
     * Logs out the user from the current session.
     * @param  bool  clear the identity from persistent storage?
     * @return void
     */
    final public function logout($clearIdentity = FALSE)
    {
        if ($this->isLoggedIn()) {
            $this->onLoggedOut($this);
            $this->storage->setAuthenticated(FALSE);
        }
        if ($clearIdentity) {
            $this->storage->setIdentity(NULL);
        }
    }

    /**
     * Is this user authenticated?
     * @return bool
     */
    final public function isLoggedIn() {
        return $this->storage->isAuthenticated();
    }

    /**
     * Returns current user identity, if any.
     * @return IIdentity|NULL
     */
    final public function getIdentity()
    {
        return $this->storage->getIdentity();
    }

    /**
     * @param string $name
     * @return mixed
     * @throws Error
     */
    public function get($name){
        return Arrays::get($this->getIdentity(),$name,null);
    }

    /**
     * Returns current user ID, if any.
     * @return mixed
     */
    public function getId()
    {
        $identity = $this->getIdentity();
        return $identity ? $identity->getId() : NULL;
    }

    /**
     * Sets authentication handler.
     * @param  IAuthenticator
     * @return User  provides a fluent interface
     */
    public function setAuthenticator(IAuthenticator $handler) {
        $this->authenticator = $handler;
        return $this;
    }

    /**
     * Returns authentication handler.
     * @return IAuthenticator
     */
    final public function getAuthenticator($need = TRUE)
    {
        if ($need && !$this->authenticator) {
            throw new Error('Authenticator has not been set.');
        }
        return $this->authenticator;
    }

    /**
     * Enables log out after inactivity.
     * @param  string|int|DateTime number of seconds or timestamp
     * @param  bool  log out when the browser is closed?
     * @param  bool  clear the identity from persistent storage?
     * @return User  provides a fluent interface
     */
    public function setExpiration($time, $whenBrowserIsClosed = TRUE, $clearIdentity = FALSE) {
        $flags = ($whenBrowserIsClosed ? IUserStorage::BROWSER_CLOSED : 0) | ($clearIdentity ? IUserStorage::CLEAR_IDENTITY : 0);
        $this->storage->setExpiration($time, $flags);
        return $this;
    }

    /**
     * Why was user logged out?
     * @return int
     */
    final public function getLogoutReason()
    {
        return $this->storage->getLogoutReason();
    }

    /**
     * Returns a list of effective roles that a user has been granted.
     * @return array
     */
    public function getRoles()
    {
        if (!$this->isLoggedIn()) {
            return array($this->guestRole);
        }

        $identity = $this->getIdentity();
        return $identity && $identity->getRoles() ? $identity->getRoles() : array($this->authenticatedRole);
    }

    /**
     * Is a user in the specified effective role?
     * @param  string
     * @return bool
     */
    final public function isInRole($role) {
        return in_array($role, $this->getRoles(), TRUE);
    }

    /**
     * Is a user in root
     * @return bool
     */
    final public function isRoleRoot(){
        return $this->isInRole($this->rootRole);
    }
    /**
     * Has a user effective access to the Resource?
     * If $resource is NULL, then the query applies to all resources.
     * @param  string  resource
     * @param  string  privilege
     * @return bool
     */
    public function isAllowed($resource = IAuthorizator::ALL, $privilege = IAuthorizator::ALL)
    {
        foreach ($this->getRoles() as $role) {
            if ($this->getAuthorizator()->isAllowed($role, $resource, $privilege)) {
                return TRUE;
            }// is root
            else if($this->getAuthorizator()->isAllowed($role, IAuthorizator::ALL, IAuthorizator::ALL)){
                return TRUE;
            }
        }

        return FALSE;
    }

    /**
     * Sets authorization handler.
     * @return User  provides a fluent interface
     */
    public function setAuthorizator(IAuthorizator $handler)
    {
        $this->authorizator = $handler;
        return $this;
    }



    /**
     * Returns current authorization handler.
     * @return IAuthorizator
     */
    final public function getAuthorizator($need = TRUE)
    {
        if ($need && !$this->authorizator) {
            throw new Error('Authorizator has not been set.');
        }
        return $this->authorizator;
    }

}