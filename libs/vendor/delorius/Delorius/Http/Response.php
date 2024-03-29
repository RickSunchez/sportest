<?php

namespace Delorius\Http;

use Delorius\Core\DateTime;
use Delorius\Core\Object;
use Delorius\Exception\BadRequest;
use Delorius\Exception\Error;
use Delorius\Utils\Strings;


final class Response extends Object implements IResponse
{
    /** @var bool  Send invisible garbage for IE 6? */
    private static $fixIE = TRUE;

    /** @var string The domain in which the cookie will be available */
    public $cookieDomain = '';

    /** @var string The path in which the cookie will be available */
    public $cookiePath = '/';

    /** @var string Whether the cookie is available only through HTTPS */
    public $cookieSecure = FALSE;

    /** @var string Whether the cookie is hidden from client-side */
    public $cookieHttpOnly = FALSE;

    /** @var bool Whether warn on possible problem with data in output buffer */
    public $warnOnBuffer = TRUE;

    /** @var int HTTP response code */
    private $code = self::S200_OK;


    public function __construct()
    {
        if (PHP_VERSION_ID >= 50400) {
            if (is_int(http_response_code())) {
                $this->code = http_response_code();
            }
        }

        if (PHP_VERSION_ID >= 50401) { // PHP bug #61106
            $rm = new \ReflectionMethod('\Delorius\Http\Helpers', 'removeDuplicateCookies');
            header_register_callback($rm->getClosure()); // requires closure due PHP bug #66375
        }
    }

    /**
     * Sets HTTP response code.
     * @param  int
     * @return Response  provides a fluent interface
     */
    public function setCode($code)
    {
        $code = (int)$code;
        if ($code < 100 || $code > 599) {
            throw new BadRequest("Bad HTTP response '$code'.");
        }
        self::checkHeaders();
        $this->code = $code;
        $protocol = isset($_SERVER['SERVER_PROTOCOL']) ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        header($protocol . ' ' . $code, TRUE, $code);
        return $this;
    }


    /**
     * Returns HTTP response code.
     * @return int
     */
    public function getCode()
    {
        return $this->code;
    }


    /**
     * Sends a HTTP header and replaces a previous one.
     * @param  string  header name
     * @param  string  header value
     * @return Response  provides a fluent interface
     */
    public function setHeader($name, $value)
    {
        self::checkHeaders();
        if ($value === NULL) {
            header_remove($name);
        } elseif (strcasecmp($name, 'Content-Length') === 0 && ini_get('zlib.output_compression')) {
            // ignore, PHP bug #44164
        } else {
            header($name . ': ' . $value, TRUE, $this->code);
        }
        return $this;
    }


    /**
     * Adds HTTP header.
     * @param  string  header name
     * @param  string  header value
     * @return Response  provides a fluent interface
     */
    public function addHeader($name, $value)
    {
        self::checkHeaders();
        header($name . ': ' . $value, FALSE, $this->code);
        return $this;
    }


    /**
     * Sends a Content-type HTTP header.
     * @param  string  mime-type
     * @param  string  charset
     * @return Response  provides a fluent interface
     */
    public function setContentType($type, $charset = NULL)
    {
        $this->setHeader('Content-Type', $type . ($charset ? '; charset=' . $charset : ''));
        return $this;
    }


    /**
     * Redirects to a new URL. Note: call exit() after it.
     * @param  string  URL
     * @param  int     HTTP code
     * @return void
     */
    public function redirect($url, $code = self::S302_FOUND)
    {
        if ($code === false) {
            die('<html><head><meta name="robots" content="nofollow" /><meta http-equiv="refresh" content="1;url=' . $url . '"><script type="text/javascript">window.location.href = "' . $url . '"</script></head><body></body></html>');
        }


        if (isset($_SERVER['SERVER_SOFTWARE']) && preg_match('#^Microsoft-IIS/[1-5]#', $_SERVER['SERVER_SOFTWARE'])
            && $this->getHeader('Set-Cookie') !== NULL
        ) {
            $this->setHeader('Refresh', "0;url=$url");
            return;
        }

        $this->setCode($code);
        $this->setHeader('Location', $url);
        if (preg_match('#^https?:|^\s*+[a-z0-9+.-]*+[^:]#i', $url)) {
            $escapedUrl = htmlSpecialChars($url, ENT_IGNORE | ENT_QUOTES, 'UTF-8');
            echo "<h1>Redirect</h1>\n\n<p><a href=\"$escapedUrl\">Please click here to continue</a>.</p>";
        }
    }


    /**
     * Sets the number of seconds before a page cached on a browser expires.
     * @param  string|int|DateTime time , value 0 means "until the browser is closed"
     * @return Response  provides a fluent interface
     */
    public function setExpiration($time)
    {
        if (!$time) {
            /* no cache*/
            $this->setHeader('Cache-Control', 's-maxage=0, max-age=0, must-revalidate');
            $this->setHeader('Expires', 'Mon, 23 Jan 1978 10:00:00 GMT');
            return $this;
        }

        $time = DateTime::from($time);
        $this->setHeader('Cache-Control', 'max-age=' . ($time->format('U') - time()));
        $this->setHeader('Expires', Helpers::formatDate($time));
        return $this;
    }


    /**
     * Checks if headers have been sent.
     * @return bool
     */
    public function isSent()
    {
        return headers_sent();
    }


    /**
     * Return the value of the HTTP header.
     * @param  string
     * @param  mixed
     * @return mixed
     */
    public function getHeader($header, $default = NULL)
    {
        $header .= ':';
        $len = strlen($header);
        foreach (headers_list() as $item) {
            if (strncasecmp($item, $header, $len) === 0) {
                return ltrim(substr($item, $len));
            }
        }
        return $default;
    }


    /**
     * Returns a list of headers to sent.
     * @return array
     */
    public function getHeaders()
    {
        $headers = array();
        foreach (headers_list() as $header) {
            $a = strpos($header, ':');
            $headers[substr($header, 0, $a)] = (string)substr($header, $a + 2);
        }
        return $headers;
    }

    /**
     * @return void
     */
    public function __destruct()
    {
        if (self::$fixIE && isset($_SERVER['HTTP_USER_AGENT']) && strpos($_SERVER['HTTP_USER_AGENT'], 'MSIE ') !== FALSE
            && in_array($this->code, array(400, 403, 404, 405, 406, 408, 409, 410, 500, 501, 505), TRUE)
            && preg_match('#^text/html(?:;|$)#', $this->getHeader('Content-Type', 'text/html'))
        ) {
            /* sends invisible garbage for IE */
            echo Strings::random(2e3, " \t\r\n");
            self::$fixIE = FALSE;
        }
    }


    /**
     * Sends a cookie.
     * @param  string name of the cookie
     * @param  string value
     * @param  string|int|DateTime expiration time, value 0 means "until the browser is closed"
     * @param  string
     * @param  string
     * @param  bool
     * @param  bool
     * @return Response  provides a fluent interface
     */
    public function setCookie($name, $value, $time = false, $path = NULL, $domain = NULL, $secure = NULL, $httpOnly = NULL)
    {
        self::checkHeaders();
        setcookie(
            $name,
            $value,
            $time ? DateTime::from($time)->format('U') : 0,
            $path === NULL ? $this->cookiePath : (string)$path,
            $domain === NULL ? $this->cookieDomain : (string)$domain,
            $secure === NULL ? $this->cookieSecure : (bool)$secure,
            $httpOnly === NULL ? $this->cookieHttpOnly : (bool)$httpOnly
        );
        Helpers::removeDuplicateCookies();
        return $this;
    }

    /**
     * Deletes a cookie.
     * @param  string name of the cookie.
     * @param  string
     * @param  string
     * @param  bool
     * @return void
     */
    public function deleteCookie($name, $path = NULL, $domain = NULL, $secure = NULL)
    {
        $this->setCookie($name, FALSE, 0, $path, $domain, $secure);
    }

    private function checkHeaders()
    {
        if (headers_sent($file, $line)) {
            throw new Error("Cannot set cookie after HTTP headers have been sent" . ($file ? " (output started at $file:$line)." : "."));

        } elseif ($this->warnOnBuffer && ob_get_length() && !array_filter(ob_get_status(TRUE), function ($i) {
                return !$i['chunk_size'];
            })
        ) {
            trigger_error('Possible problem: you are sending a HTTP header while already having some data in output buffer. Try Tracy\OutputDebugger or start session earlier.', E_USER_NOTICE);
        }
    }

}
