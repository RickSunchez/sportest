<?php
namespace Delorius\Http;

use Delorius\Core\DateTime;
use Delorius\Exception\Error;
use Delorius\Core\Framework;
use Delorius\Core\Object;
use Delorius\Reflection\ClassType;
use Delorius\Utils\Callback;
use Delorius\Utils\Strings;

/**
 * Provides access to session sections as well as session settings and management methods.
 * @property-read bool $started
 * @property-read string $id
 * @property   string $name
 * @property-read \ArrayIterator $iterator
 * @property   array $options
 * @property-write $savePath
 * @property-write ISessionStorage $storage
 */
class Session extends Object
{
    /** Default file lifetime is 3 hours = 10800*/
    const DEFAULT_FILE_LIFETIME = 1440;

    /** @var bool  has been session ID regenerated? */
    private $regenerated;

    /** @var bool  has been session started? */
    private static $started;


    /** @var array default coDFiguration */
    private $options = array(
        // security
        'referer_check' => '',    // must be disabled because PHP implementation is invalid
        'use_cookies' => 1,       // must be enabled to prevent Session Hijacking and Fixation
        'use_only_cookies' => 1,  // must be enabled to prevent Session Fixation
        'use_trans_sid' => 0,     // must be disabled to prevent Session Hijacking and Fixation

        // cookies
        'cookie_lifetime' => 0,   // until the browser is closed
        'cookie_path' => '/',     // cookie is available within the entire domain
        'cookie_domain' => '',    // cookie is available on current subdomain only
        'cookie_secure' => FALSE, // cookie is available on HTTP & HTTPS
        'cookie_httponly' => TRUE,// must be enabled to prevent Session Hijacking
        'name' => 'DELORIUSSESSID',

        // other
        'gc_maxlifetime' => self::DEFAULT_FILE_LIFETIME,// 3 hours
        'cache_limiter' => NULL,  // (default "nocache", special value "\0")
        'cache_expire' => NULL,   // (default "180")
        'hash_function' => NULL,  // (default "0", means MD5)
        'hash_bits_per_character' => NULL, // (default "4")
    );

    /** @var IRequest */
    private $request;

    /** @var IResponse */
    private $response;

    /** @var \SessionHandlerInterface */
    private $handler;


    public function __construct(IRequest $request, IResponse $response)
    {
        $this->request = $request;
        $this->response = $response;
    }


    /**
     * Starts and initializes session data.
     * @throws Error
     * @return void
     */
    public function start()
    {
        if (self::$started) {
            return;
        }

        $this->configure($this->options);

        $id = &$_COOKIE[session_name()];
        if (!is_string($id) || !preg_match('#^[0-9a-zA-Z,-]{22,128}\z#i', $id)) {
            unset($_COOKIE[session_name()]);
        }

        try {
            // session_start returns FALSE on failure only sometimes
            Callback::invokeSafe('session_start', array(), function ($message) use (& $e) {
                $e = new Error($message);
            });
        } catch (\Exception $e) {
        }

        Helpers::removeDuplicateCookies();

        if ($e) {
            @session_write_close(); // this is needed
            throw $e;
        }

        self::$started = TRUE;

        /* structure:
            __DF: BrowserKey, Data, Meta, Time
                DATA: section->variable = data
                META: section->variable = Timestamp, Browser, Version
        */
        $DF = &$_SESSION['__DF'];

        // regenerate empty session
        if (empty($DF['Time'])) {
            $DF['Time'] = time();
            $this->regenerated = TRUE;
        }

        // browser closing detection
        $browserKey = $this->request->getCookie('delorius-browser');
        if (!$browserKey) {
            $browserKey = Strings::random();
        }
        $browserClosed = !isset($DF['B']) || $DF['B'] !== $browserKey;
        $DF['B'] = $browserKey;

        // resend cookie
        $this->sendCookie();

        // process meta metadata
        if (isset($DF['META'])) {
            $now = time();
            // expire section variables
            foreach ($DF['META'] as $section => $metadata) {
                if (is_array($metadata)) {
                    foreach ($metadata as $variable => $value) {
                        if ((!empty($value['B']) && $browserClosed) || (!empty($value['T']) && $now > $value['T']) // whenBrowserIsClosed || Time
                            || (isset($DF['DATA'][$section][$variable]) && is_object($DF['DATA'][$section][$variable]) && (isset($value['V']) ? $value['V'] : NULL) // Version
                                != ClassType::from($DF['DATA'][$section][$variable])->getAnnotation('serializationVersion')) // intentionally !=
                        ) {
                            if ($variable === '') { // expire whole section
                                unset($DF['META'][$section], $DF['DATA'][$section]);
                                continue 2;
                            }
                            unset($DF['META'][$section][$variable], $DF['DATA'][$section][$variable]);
                        }
                    }
                }
            }
        }

        if ($this->regenerated) {
            $this->regenerated = FALSE;
            $this->regenerateId();
        }

        register_shutdown_function(array($this, 'clean'));
    }


    /**
     * Has been session started?
     * @return bool
     */
    public function isStarted()
    {
        return (bool)self::$started;
    }


    /**
     * Ends the current session and store session data.
     * @return void
     */
    public function close()
    {
        if (self::$started) {
            $this->clean();
            session_write_close();
            self::$started = FALSE;
        }
    }


    /**
     * Destroys all data registered to a session.
     * @return void
     */
    public function destroy()
    {
        if (!self::$started) {
            throw new Error('Session is not started.');
        }

        session_destroy();
        $_SESSION = NULL;
        self::$started = FALSE;
        if (!$this->response->isSent()) {
            $params = session_get_cookie_params();
            $this->response->deleteCookie(session_name(), $params['path'], $params['domain'], $params['secure']);
        }
    }


    /**
     * Does session exists for the current request?
     * @return bool
     */
    public function exists()
    {
        return self::$started || $this->request->getCookie($this->getName()) !== NULL;
    }


    /**
     * Regenerates the session ID.
     * @throws Error
     * @return void
     */
    public function regenerateId()
    {
        if (self::$started && !$this->regenerated) {
            if (headers_sent($file, $line)) {
                throw new Error("Cannot regenerate session ID after HTTP headers have been sent" . ($file ? " (output started at $file:$line)." : "."));
            }
            if (session_id() !== '') {
                session_regenerate_id(TRUE);
            }
            session_write_close();
            $backup = $_SESSION;
            session_start();
            $_SESSION = $backup;
            Helpers::removeDuplicateCookies();
        }
        $this->regenerated = TRUE;
    }


    /**
     * Returns the current session ID. Don't make dependencies, can be changed for each request.
     * @return string
     */
    public function getId()
    {
        return session_id();
    }


    /**
     * Sets the session name to a specified one.
     * @param  string
     * @return Session  provides a fluent interface
     */
    public function setName($name)
    {
        if (!is_string($name) || !preg_match('#[^0-9.][^.]*\z#A', $name)) {
            throw new Error('Session name must be a string and cannot contain dot.');
        }

        session_name($name);
        return $this->setOptions(array(
            'name' => $name,
        ));
    }


    /**
     * Gets the session name.
     * @return string
     */
    public function getName()
    {
        return isset($this->options['name']) ? $this->options['name'] : session_name();
    }



    /********************* sections management ****************d*g**/


    /**
     * Returns specified session section.
     * @param  string
     * @param  string
     * @return SessionSection
     * @throws Error
     */
    public function getSection($section, $class = '\Delorius\Http\SessionSection')
    {
        return new $class($this, $section);
    }


    /**
     * Checks if a session section exist and is not empty.
     * @param  string
     * @return bool
     */
    public function hasSection($section)
    {
        if ($this->exists() && !self::$started) {
            $this->start();
        }

        return !empty($_SESSION['__DF']['DATA'][$section]);
    }


    /**
     * Iteration over all sections.
     * @return \ArrayIterator
     */
    public function getIterator()
    {
        if ($this->exists() && !self::$started) {
            $this->start();
        }

        if (isset($_SESSION['__DF']['DATA'])) {
            return new \ArrayIterator(array_keys($_SESSION['__DF']['DATA']));

        } else {
            return new \ArrayIterator;
        }
    }


    /**
     * Cleans and minimizes meta structures.
     * @return void
     */
    public function clean()
    {
        if (!self::$started || empty($_SESSION)) {
            return;
        }

        $DF = &$_SESSION['__DF'];
        if (isset($DF['META']) && is_array($DF['META'])) {
            foreach ($DF['META'] as $name => $foo) {
                if (empty($DF['META'][$name])) {
                    unset($DF['META'][$name]);
                }
            }
        }

        if (empty($DF['META'])) {
            unset($DF['META']);
        }

        if (empty($DF['DATA'])) {
            unset($DF['DATA']);
        }
    }



    /********************* coDFiguration ****************d*g**/


    /**
     * Sets session options.
     * @param  array
     * @return Session  provides a fluent interface
     * @throws Error
     */
    public function setOptions(array $options)
    {
        if (self::$started) {
            $this->configure($options);
        }
        $this->options = $options + $this->options;
        if (!empty($options['auto_start'])) {
            $this->start();
        }
        return $this;
    }


    /**
     * Returns all session options.
     * @return array
     */
    public function getOptions()
    {
        return $this->options;
    }


    /**
     * Configurates session environment.
     * @param  array
     * @return void
     */
    private function configure(array $config)
    {
        $special = array('cache_expire' => 1, 'cache_limiter' => 1, 'save_path' => 1, 'name' => 1);

        foreach ($config as $key => $value) {
            if (!strncmp($key, 'session.', 8)) { // back compatibility
                $key = substr($key, 8);
            }
            $key = strtolower(preg_replace('#(.)(?=[A-Z])#', '$1_', $key));

            if ($value === NULL || ini_get("session.$key") == $value) { // intentionally ==
                continue;

            } elseif (strncmp($key, 'cookie_', 7) === 0) {
                if (!isset($cookie)) {
                    $cookie = session_get_cookie_params();
                }
                $cookie[substr($key, 7)] = $value;

            } else {
                if (defined('SID')) {
                    throw new Error("Unable to set 'session.$key' to value '$value' when session has been started" . ($this->started ? "." : " by session.auto_start or session_start()."));
                }
                if (isset($special[$key])) {
                    $key = "session_$key";
                    $key($value);

                } elseif (function_exists('ini_set')) {
                    ini_set("session.$key", $value);

                } elseif (!Framework::$iAmUsingBadHost) {
                    throw new Error('Required function ini_set() is disabled.');
                }
            }
        }

        if (isset($cookie)) {
            session_set_cookie_params(
                $cookie['lifetime'], $cookie['path'], $cookie['domain'],
                $cookie['secure'], $cookie['httponly']
            );
            if (self::$started) {
                $this->sendCookie();
            }
        }

        if ($this->handler) {
            session_set_save_handler($this->handler);
        }
    }


    /**
     * Sets the amount of time allowed between requests before the session will be terminated.
     * @param  string|int|DateTime time , value 0 means "until the browser is closed"
     * @return Session  provides a fluent interface
     */
    public function setExpiration($time)
    {
        if (empty($time)) {
            return $this->setOptions(array(
                'gc_maxlifetime' => self::DEFAULT_FILE_LIFETIME,
                'cookie_lifetime' => 0,
            ));

        } else {
            $time = DateTime::from($time)->format('U') - time();
            return $this->setOptions(array(
                'gc_maxlifetime' => $time,
                'cookie_lifetime' => $time,
            ));
        }
    }


    /**
     * Sets the session cookie parameters.
     * @param  string  path
     * @param  string  domain
     * @param  bool    secure
     * @return Session  provides a fluent interface
     */
    public function setCookieParameters($path, $domain = NULL, $secure = NULL)
    {
        return $this->setOptions(array(
            'cookie_path' => $path,
            'cookie_domain' => $domain,
            'cookie_secure' => $secure
        ));
    }


    /**
     * Returns the session cookie parameters.
     * @return array  containing items: lifetime, path, domain, secure, httponly
     */
    public function getCookieParameters()
    {
        return session_get_cookie_params();
    }


    /**
     * Sets path of the directory used to save session data.
     * @return Session  provides a fluent interface
     */
    public function setSavePath($path)
    {
        return $this->setOptions(array(
            'save_path' => $path,
        ));
    }


    /**
     * Sets user session storage.
     * @return Session  provides a fluent interface
     */
    public function setStorage(ISessionStorage $storage)
    {
        if (self::$started) {
            throw new Error("Unable to set storage when session has been started.");
        }
        session_set_save_handler(
            array($storage, 'open'), array($storage, 'close'), array($storage, 'read'),
            array($storage, 'write'), array($storage, 'remove'), array($storage, 'clean')
        );
    }

    /**
     * Sets user session handler.
     * @return self
     */
    public function setHandler(\SessionHandlerInterface $handler)
    {
        if (self::$started) {
            throw new Error('Unable to set handler when session has been started.');
        }
        $this->handler = $handler;
        return $this;
    }


    /**
     * Sends the session cookies.
     * @return void
     */
    private function sendCookie()
    {
        $cookie = $this->getCookieParameters();
        $this->response->setCookie(
            session_name(), session_id(),
            $cookie['lifetime'] ? $cookie['lifetime'] + time() : 0,
            $cookie['path'], $cookie['domain'], $cookie['secure'], $cookie['httponly']

        )->setCookie(
            'delorius-browser', $_SESSION['__DF']['B'],
            Response::BROWSER, $cookie['path'], $cookie['domain']
        );
    }

}
