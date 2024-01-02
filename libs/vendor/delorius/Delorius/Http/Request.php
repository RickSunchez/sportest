<?php
namespace Delorius\Http;

use Delorius\Core\Object;
use Delorius\Utils\Arrays;

Class Request extends Object implements IRequest
{
    /** @var string */
    private $method;

    /** @var UrlScript */
    private $url;

    /** @var array */
    private $post;

    /** @var array */
    private $files;

    /** @var array */
    private $cookies;

    /** @var array */
    private $headers;

    /** @var string */
    private $remoteAddress;

    /** @var string */
    private $remoteHost;

    /** @var callable|NULL */
    private $rawBodyCallback;

    public function __construct(
        UrlScript $url,
        $query = array(),
        $post = NULL,
        $files = NULL,
        $cookies = NULL,
        $headers = NULL,
        $method = NULL,
        $remoteAddress = NULL,
        $remoteHost = NULL,
        $rawBodyCallback = NULL)
    {
        $this->url = $url;
        if ($query !== NULL) {
            $url->setQuery($query);
        }

        $this->post = (array)$post;
        $this->files = (array)$files;
        $this->cookies = (array)$cookies;
        $this->headers = array_change_key_case((array)$headers, CASE_LOWER);
        $this->method = $method ?: 'GET';
        $this->remoteAddress = $remoteAddress;
        $this->remoteHost = $remoteHost;
        $this->rawBodyCallback = $rawBodyCallback;
    }


    public function getUrl()
    {
        return clone $this->url;
    }

    public function getQuery($key = NULL, $default = NULL)
    {
        if (func_num_args() === 0) {
            return $this->url->getQueryParameters();
        } else {
            return $this->url->getQueryParameter($key, $default);
        }
    }

    public function getPost($key = NULL, $default = NULL)
    {
        //принимает json post
        if (!count($this->post) && $this->isPost()) {
            $this->post = (array)json_decode($this->getRawBody(), true);
        }

        if (func_num_args() === 0) {
            return $this->post;

        } elseif (isset($this->post[$key])) {
            return $this->post[$key];

        } else {
            return $default;
        }
    }

    public function getRequest($key = NULL, $default = NULL)
    {
        $request = array_merge($this->getPost(), $this->getQuery());

        if (func_num_args() === 0) {
            return $request;
        } elseif (isset($request[$key])) {
            return $request[$key];
        } else {
            return $default;
        }
    }

    public function getCookie($key = NULL, $default = NULL)
    {
        if (func_num_args() === 0) {
            return $this->cookies;

        } elseif (isset($this->cookies[$key])) {
            return $this->cookies[$key];

        } else {
            return $default;
        }
    }

    public function getCookies()
    {
        return $this->cookies;
    }

    public function getFile($key)
    {
        return Arrays::get($this->files, $key, NULL);
    }

    public function getFiles()
    {
        return $this->files;
    }

    /**
     * Returns HTTP request method (GET, POST, HEAD, PUT, ...). The method is case-sensitive.
     * @return string
     */
    public function getMethod()
    {
        return $this->method;
    }

    /**
     * Checks if the request method is the given one.
     * @param  string
     * @return bool
     */
    public function isMethod($method)
    {
        return strcasecmp($this->method, $method) === 0;
    }


    /**
     * Checks if the request method is POST.
     * @return bool
     */
    public function isPost()
    {
        return $this->isMethod('POST');
    }


    /**
     * Return the value of the HTTP header. Pass the header name as the
     * plain, HTTP-specified header name (e.g. 'Accept-Encoding').
     * @param  string
     * @param  mixed
     * @return mixed
     */
    final public function getHeader($header, $default = NULL)
    {
        $header = strtolower($header);
        return isset($this->headers[$header]) ? $this->headers[$header] : $default;
    }


    /**
     * Returns all HTTP headers.
     * @return array
     */
    public function getHeaders()
    {
        return $this->headers;
    }


    /**
     * Returns referrer.
     * @return Url|NULL
     */
    final public function getReferer()
    {
        return isset($this->headers['referer']) ? new Url($this->headers['referer']) : NULL;
    }


    /**
     * Is the request is sent via secure channel (https).
     * @return bool
     */
    public function isSecured()
    {
        return $this->url->scheme === 'https';
    }


    /**
     * Is AJAX request?
     * @return bool
     */
    public function isAjax()
    {
        return $this->getHeader('X-Requested-With') === 'XMLHttpRequest';
    }


    /**
     * Returns the IP address of the remote client.
     * @return string
     */
    public function getRemoteAddress()
    {
        return $this->remoteAddress;
    }


    /**
     * Returns the host of the remote client.
     * @return string
     */
    public function getRemoteHost()
    {
        if ($this->remoteHost === NULL && $this->remoteAddress !== NULL) {
            $this->remoteHost = getHostByAddr($this->remoteAddress);
        }
        return $this->remoteHost;
    }

    /**
     * Returns raw content of HTTP request body.
     * @return string|NULL
     */
    public function getRawBody()
    {
        return $this->rawBodyCallback ? call_user_func($this->rawBodyCallback) : NULL;
    }


    /**
     * Parse Accept-Language header and returns prefered language.
     * @param  array   Supported languages
     * @return string
     */
    public function detectLanguage(array $langs)
    {
        $header = $this->getHeader('Accept-Language');
        if (!$header)
            return NULL;

        $s = strtolower($header); /*/ case insensitive*/
        $s = strtr($s, '_', '-'); /*/ ru_RU means ru-RU*/
        rsort($langs); /*/ first more specific*/
        preg_match_all('#(' . implode('|', $langs) . ')(?:-[^\s,;=]+)?\s*(?:;\s*q=([0-9.]+))?#', $s, $matches);

        if (!$matches[0])
            return NULL;

        $max = 0;
        $lang = NULL;
        foreach ($matches[1] as $key => $value) {
            $q = $matches[2][$key] === '' ? 1.0 : (float)$matches[2][$key];
            if ($q > $max) {
                $max = $q;
                $lang = $value;
            }
        }

        return $lang;
    }
}