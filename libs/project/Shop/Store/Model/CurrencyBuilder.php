<?php
namespace Shop\Store\Model;

use Delorius\Core\Object;
use Delorius\DI\Container;
use Delorius\Http\IRequest;
use Delorius\Http\IResponse;
use Shop\Store\Entity\Currency;

class CurrencyBuilder extends Object
{

    /**
     * @var \Delorius\Http\IRequest
     */
    protected $httpRequest;

    /**
     * @var \Delorius\Http\IResponse
     */
    protected $httpResponse;

    const NAME = 'currency';

    /** @var  string code currency */
    protected $code;
    /** @var array Shop\Store\Entity\Currency */
    private $_currencies = array();
    /** @var array config shop */
    protected $config = array();

    public function __construct(Container $container, IRequest $request, IResponse $response)
    {
        $this->config = $container->getParameters('shop.store.currency');
        $this->httpRequest = $request;
        $this->httpResponse = $response;
        if ($this->config['init']) {
            $this->init();
        }
    }

    protected function init()
    {
        $currencies = Currency::model()
            ->select()
            ->cached()
            ->order_pk()
            ->find_all();
        foreach ($currencies as $cur) {
            $this->_currencies[$cur['code']] = $cur;
        }
    }

    public function setting()
    {
        if ($this->has($this->httpRequest->getQuery(self::NAME, null))) {
            $this->set($this->httpRequest->getQuery(self::NAME));
        } elseif ($this->has($this->httpRequest->getCookie(self::NAME, null))) {
            $this->set($this->httpRequest->getCookie(self::NAME));
        } else {
            $this->set($this->config['code']);
        }
    }

    public function set($currency)
    {
        $this->code = $currency;
        $this->httpResponse->setCookie(self::NAME, $currency, '+ 1 year', '/', getDomainCookie());
    }

    public function format($sum, $from, $to = null, $format = true)
    {
        if ($to && $this->has($to)) {
            $symbol_left = $this->_currencies[$to]['symbol_left'];
            $symbol_right = $this->_currencies[$to]['symbol_right'];
            $decimal_place = $this->_currencies[$to]['decimal_place'];
            $currency = $to;
        } else {
            $symbol_left = $this->_currencies[$this->code]['symbol_left'];
            $symbol_right = $this->_currencies[$this->code]['symbol_right'];
            $decimal_place = $this->_currencies[$this->code]['decimal_place'];
            $currency = $this->code;
        }

        $value = $this->convert($sum, $from, $currency);

        $string = '';

        if (($symbol_left) && ($format)) {
            $string .= $symbol_left . ' ';
        }

        if ($format) {
            $decimal_point = $this->config['decimal_point'];
            $thousand_point = $this->config['thousand_point'];
        } else {
            $decimal_point = '.';
            $thousand_point = '';
        }

        if ($this->_currencies[$currency]['decimal_type'] == Currency::TYPE_DECIMAL) {
            if (($value - floor($value)) == 0) {
                $decimal_place = 0;
            }
        } else if ($this->_currencies[$currency]['decimal_type'] == Currency::TYPE_NO_DECIMAL) {
            $decimal_place = 0;
        }

        $string .= number_format(
            round($value, (int)$decimal_place),
            (int)$decimal_place,
            $decimal_point,
            $thousand_point
        );

        if (($symbol_right) && ($format)) {
            $string .= '' . $symbol_right;
        }

        return $string;
    }

    public function convert($sum, $from, $to = null)
    {
        if ($from == $to) {
            return $sum;
        }

        if (!$this->has($from)) {
            return 0;
        }

        if (!$to && !$this->has($to)) {
            $to = $this->code;
        }

        //Считаем и возвращаем сконвертированную сумму
        return ($sum * $this->_currencies[$from]['value'] / $this->_currencies[$to]['value'])
        / $this->_currencies[$from]['nominal'] * $this->_currencies[$to]['nominal'];
    }


    public function getId($currency = null)
    {
        if (!$currency) {
            return $this->_currencies[$this->code]['currency_id'];
        } elseif ($currency && isset($this->_currencies[$currency])) {
            return $this->_currencies[$currency]['currency_id'];
        } else {
            return 0;
        }
    }

    public function getSymbolLeft($currency = null)
    {
        if (!$currency) {
            return $this->_currencies[$this->code]['symbol_left'];
        } elseif ($currency && isset($this->_currencies[$currency])) {
            return $this->_currencies[$currency]['symbol_left'];
        } else {
            return '';
        }
    }

    public function getSymbolRight($currency = null)
    {
        if (!$currency) {
            return $this->_currencies[$this->code]['symbol_right'];
        } elseif ($currency && isset($this->_currencies[$currency])) {
            return $this->_currencies[$currency]['symbol_right'];
        } else {
            return '';
        }
    }

    public function getDecimalPlace($currency = null)
    {
        if (!$currency) {
            return $this->_currencies[$this->code]['decimal_place'];
        } elseif ($currency && isset($this->_currencies[$currency])) {
            return $this->_currencies[$currency]['decimal_place'];
        } else {
            return 0;
        }
    }

    public function getCode()
    {
        return $this->code;
    }

    public function getValue($currency = '')
    {
        if (!$currency) {
            return $this->_currencies[$this->code]['value'];
        } elseif ($currency && isset($this->_currencies[$currency])) {
            return $this->_currencies[$currency]['value'];
        } else {
            return 0;
        }
    }

    public function has($currency)
    {
        return isset($this->_currencies[$currency]);
    }
} 