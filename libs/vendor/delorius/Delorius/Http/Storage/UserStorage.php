<?php
namespace Delorius\Http\Storage;

use Delorius\Core\DateTime;
use Delorius\Core\Object;
use Delorius\Http\Session;
use Delorius\Http\SessionSection;
use Delorius\Security\IIdentity;
use Delorius\Security\IUserStorage;

Class UserStorage extends Object implements IUserStorage {

    /* названия сессии */
    /** @var string */
    private $namespace = '';
    /** @var Session */
    private $sessionHandler;
    /** @var SessionSection */
    private $sessionSection;

    protected $sessionName = 'user_session_13281837';

    public function __construct(Session $sessionHandler) {
        $this->sessionHandler = $sessionHandler;
    }

    /**
     * Sets the authenticated status of this user.
     * @param  bool
     * @return UserStorage Provides a fluent interface
     */
    public function setAuthenticated($state) {
        $section = $this->getSessionSection(TRUE);
        $section->authenticated = (bool) $state;

        $this->sessionHandler->regenerateId();

        if ($state) {
            $section->reason = NULL;
            $section->authTime = time();
        } else {
            $section->reason = self::MANUAL;
            $section->authTime = NULL;
        }

        return $this;
    }

    /**
     * Is this user authenticated?
     * @return bool
     */
    public function isAuthenticated() {
        $session = $this->getSessionSection(FALSE);
        return $session && $session->authenticated;
    }

    /**
     * Sets the user identity.
     * @param  Identity
     * @return UserStorage Provides a fluent interface
     */
    public function setIdentity(IIdentity $identity = NULL) {
        $this->getSessionSection(TRUE)->identity = $identity;
        return $this;
    }

    /**
     * Returns current user identity, if any.
     * @return  Identity|NULL
     */
    public function getIdentity() {
        $session = $this->getSessionSection(FALSE);
        return $session ? $session->identity : NULL;
    }

    /**
     * Changes namespace; allows more users to share a session.
     * @param  string
     * @return UserStorage Provides a fluent interface
     */
    public function setNamespace($namespace)
    {
        if ($this->namespace !== $namespace) {
            $this->namespace = (string) $namespace;
            $this->sessionSection = NULL;
        }
        return $this;
    }

    /**
     * Returns current namespace.
     * @return string
     */
    public function getNamespace()
    {
        return $this->namespace;
    }

    /**
     * Return session section user
     * @param type $need
     * @return session Section 
     */
    protected function getSessionSection($need) {
        if ($this->sessionSection !== NULL) {
            return $this->sessionSection;
        }

        if (!$need && !$this->sessionHandler->exists()) {
            return NULL;
        }

        $this->sessionSection = $section = $this->sessionHandler->getSection('Delorius.Http.UserStorage/' . $this->namespace);

        if (!$section->identity instanceof IIdentity || !is_bool($section->authenticated)) {
            $section->remove();
        }

        if ($section->authenticated && $section->expireBrowser && !$section->browserCheck) { // check if browser was closed?
            $section->reason = self::BROWSER_CLOSED;
            $section->authenticated = FALSE;
            if ($section->expireIdentity) {
                unset($section->identity);
            }
        }

        if ($section->authenticated && $section->expireDelta > 0) {

            if ($section->expireTime < time()) {
                $section->authenticated = false;
                $section->reason = self::INACTIVITY;
                if ($section->expireIdentity) {
                    unset($section->identity);
                }
            }

            $section->expireTime = time() + $section->expireDelta;
        }

        if (!$section->authenticated) {
            unset($section->expireTime, $section->expireDelta, $section->expireIdentity, $section->expireBrowser, $section->browserCheck, $section->authTime);
        }


        return $this->sessionSection;
    }

    /**
     * Enables log out after inactivity.
     * @param  string|int|DateTime Number of seconds or timestamp      
     * @return UserStorage Provides a fluent interface
     */
    public function setExpiration($time, $flags = 0) {
        $section = $this->getSessionSection(TRUE);
        if ($time) {
            $time = DateTime::from($time)->format('U');
            $section->expireTime = $time;
            $section->expireDelta = $time - time();
        } else {
            unset($section->expireTime, $section->expireDelta);
        }

        $section->expireIdentity = (bool) ($flags & self::CLEAR_IDENTITY);
        $section->expireBrowser = (bool) ($flags & self::BROWSER_CLOSED);
        $section->browserCheck = TRUE;
        $section->setExpiration(0, 'browserCheck');
        $section->setExpiration($time, 'foo'); // time check
        return $this;
    }

    /**
     * Why was user logged out?
     * @return int
     */
    public function getLogoutReason() {
        $session = $this->getSessionSection(FALSE);
        return $session ? $session->reason : NULL;
    }

}