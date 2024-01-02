<?php

namespace Location\Core\Model;

use Location\Core\Entity\City;
use Delorius\Core\Object;
use Delorius\Http\IRequest;
use Delorius\Http\IResponse;

class CitiesBuilder extends Object
{
    /**
     * @var \Delorius\Http\IRequest
     */
    protected $httpRequest;

    /**
     * @var \Delorius\Http\IResponse
     */
    protected $httpResponse;

    const NAME = 'city';

    /** @var  string code city */
    protected $code;
    /** @var  string main code city */
    protected $default;
    /** @var array */
    private $_cities = array();

    public function __construct(IRequest $request, IResponse $response)
    {
        $this->httpRequest = $request;
        $this->httpResponse = $response;
        $this->init();
        $this->setting();
    }

    protected function init()
    {
        $cities = City::model()
            ->active()
            ->select('name', 'name_2', 'name_3', 'name_4', 'id', 'url', 'main')
            ->cached()
            ->find_all();
        foreach ($cities as $city) {
            if ($city['main'] == 1) {
                $this->code = $city['url'];
                $this->default = $city['url'];
            }
            $this->_cities[$city['url']] = $city;
        }
    }

    public function setting()
    {
        if ($this->has($this->httpRequest->getQuery(self::NAME, null))) {
            $this->set($this->httpRequest->getQuery(self::NAME));
        } elseif ($this->has($this->httpRequest->getCookie(self::NAME, null))) {
            $this->set($this->httpRequest->getCookie(self::NAME));
        } else {
            $this->set($this->code);
        }
    }

    public function set($code)
    {
        $this->code = $code;
        $this->httpResponse->setCookie(self::NAME, $code, '+ 1 year', '/', getDomainCookie());
    }

    public function get($code = null)
    {
        if (!$code && $this->code !== null) {
            return $this->_cities[$this->code];
        } elseif ($code && $this->has($code)) {
            return $this->_cities[$code];
        } else {
            return array();
        }
    }

    public function getId($code = null)
    {
        if (!$code) {
            return $this->_cities[$this->code]['id'];
        } elseif ($code && isset($this->_cities[$code])) {
            return $this->_cities[$code]['id'];
        } else {
            return 0;
        }
    }

    public function getName($code = null)
    {
        if (!$code && $this->code !== null) {
            return $this->_cities[$this->code]['name'];
        } elseif ($code && $this->has($code)) {
            return $this->_cities[$code]['name'];
        } else {
            return '';
        }
    }

    public function getName2($code = null)
    {
        if (!$code && $this->code !== null) {
            return $this->_cities[$this->code]['name_2'];
        } elseif ($code && $this->has($code)) {
            return $this->_cities[$code]['name_2'];
        } else {
            return $this->getName($code);
        }
    }

    public function getName3($code = null)
    {
        if (!$code && $this->code !== null) {
            return $this->_cities[$this->code]['name_3'];
        } elseif ($code && $this->has($code)) {
            return $this->_cities[$code]['name_3'];
        } else {
            return $this->getName($code);
        }
    }

    public function getName4($code = null)
    {
        if (!$code && $this->code !== null) {
            return $this->_cities[$this->code]['name_4'];
        } elseif ($code && $this->has($code)) {
            return $this->_cities[$code]['name_4'];
        } else {
            return $this->getName($code);
        }
    }


    public function getUrl($code = null)
    {
        if (!$code && $this->code !== null) {
            return $this->_cities[$this->code]['url'];
        } elseif ($code && $this->has($code)) {
            return $this->_cities[$code]['url'];
        } else {
            return '';
        }
    }

    public function has($code)
    {
        return isset($this->_cities[$code]);
    }

    public function setDefault()
    {
        $this->set($this->default);
    }

    public function getDefault()
    {
        return $this->get($this->default);
    }

    public function isDefault()
    {
        return $this->default == $this->code;
    }

    public function getAttr($code)
    {
        $data = $this->get($this->code);
        $city = City::mock($data);
        $options = $city->getOptions();
        $result = array();
        foreach ($options as $opt) {
            $result[$opt['code']] = $opt['value'];
        }
        return $result[$code];
    }
} 