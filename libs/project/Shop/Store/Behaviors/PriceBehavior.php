<?php
namespace Shop\Store\Behaviors;

use Delorius\Behaviors\ORMBehavior;

class PriceBehavior extends ORMBehavior
{
    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /**
     * Указать имя поня где есть ISO код денег
     * @var string
     */
    public $field_code;

    /**
     * @return float|int
     */
    public function getValue()
    {
        return $this->currency->convert($this->getOwner()->value, $this->getCode(), SYSTEM_CURRENCY);
    }

    /**
     * Возращает стоимость в указаной валюте
     * @param null $value
     * @param bool $format
     * @return string
     */
    public function getPrice($value = null, $format = true)
    {
        $value = $value ? $value : $this->getOwner()->value;
        if ($value == 0) {
            return '';
        }
        return $this->currency->format($value, $this->getCode(), null, $format);
    }

    /**
     * Код ISO валюты
     * @return string
     */
    protected function getCode()
    {
        if ($this->field_code) {
            $code = $this->getOwner()->get($this->field_code);
            if ($code != null) {
                return $code;
            } else {
                return SYSTEM_CURRENCY;
            }
        } else {
            return SYSTEM_CURRENCY;
        }
    }

}