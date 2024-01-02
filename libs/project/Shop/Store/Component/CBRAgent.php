<?php
namespace Shop\Store\Component;

class CBRAgent
{
    protected $list = array();

    /**
     * @return bool
     */
    public function load()
    {
        $xml = new \DOMDocument();
        $url = 'http://www.cbr.ru/scripts/XML_daily.asp';

        if (@$xml->load($url))
        {
            $this->list = array();

            $root = $xml->documentElement;
            $items = $root->getElementsByTagName('Valute');

            foreach ($items as $item)
            {
                $code = $item->getElementsByTagName('CharCode')->item(0)->nodeValue;
                $nominal = $item->getElementsByTagName('Nominal')->item(0)->nodeValue;
                $curs = $item->getElementsByTagName('Value')->item(0)->nodeValue;
                $this->list[$code] = array(
                    'value'=>floatval(str_replace(',', '.', $curs)),
                    'nominal'=>$nominal
                );

            }

            return true;
        }
        else
            return false;
    }

    /**
     * @param string $currency
     * @return bool|array
     */
    public function get($currency)
    {
        return isset($this->list[$currency]) ? $this->list[$currency] : false;
    }
}