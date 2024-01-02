<?php
namespace Shop\Store\Behaviors;

use Delorius\Behaviors\ORMBehavior;

class HelperPriceBehavior extends ORMBehavior
{
    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /**
     * Стоимость товара в системной валюте
     * @return float|int
     */
    public function getValue(){
        return $this->currency->convert($this->getOwner()->value,$this->getOwner()->code,SYSTEM_CURRENCY);
    }

    /**
     * Получения стоимости в выбраной валюте
     * @param bool $format
     * @return string
     */
    public function getPrice($format = true,$from = true){
        $s = '';
        if($this->getOwner()->value_of && $from){
            $s .= _t('Shop:Store','from');
        }
        return $s.$this->currency->format($this->getOwner()->value,$this->getOwner()->code,null,$format);
    }

    /**
     * Получения старой стоимости товара в выбраной валюте
     * @param bool $format
     * @return string
     */
    public function getPriceOld($format = true){
        return $this->currency->format($this->getOwner()->value_old,$this->getOwner()->code,null,$format);
    }

}