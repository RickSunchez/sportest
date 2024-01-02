<?php
namespace Shop\Store\Component\Cart;

use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Utils\Strings;
use Delorius\Utils\Validators;
use Shop\Commodity\Entity\Goods;
use Shop\Store\Entity\Order;
use Shop\Store\Model\CurrencyBuilder;

class Helpers
{

    const DEFAULT_STEP = 1;

    /**
     * @param $quantity
     * @param $minimum
     * @param $maximum
     * @param $step
     * @return float|number
     */
    public static function calc($quantity, $minimum = 0, $maximum = 0, $step = 0, $default = self::DEFAULT_STEP)
    {
        $quantity = abs($quantity);
        $minimum = abs($minimum);
        $maximum = abs($maximum);
        $step = abs($step);
        if ($quantity < $minimum) {
            $quantity = ($minimum == 0 ? ($step == 0 ? $default : $step) : $minimum);
        } elseif ($quantity > $maximum && $maximum != 0) {
            $quantity = $maximum;
        } else if ($step != 0) {
            $rem = $quantity / $step;
            if (((int)$rem - (float)$rem) != 0) {
                $div = $quantity / $step;
                $quantity = (int)$div * $step + $step;
            }
        }
        return $quantity;
    }

    /**
     * @param int|float $value
     * @param int $decimals
     * @return string
     */
    public static function digitFormat($value, $decimals = 3)
    {
        if (($value - floor($value)) == 0) {
            $decimals = 0;
        }
        // французский формат
        // 1234.56;
        // 1 234,56
        return number_format($value, $decimals, ',', '');
    }

    /**
     * @param $value
     * @return mixed
     */
    public static function float($value)
    {
        return str_replace(',', '.', $value);
    }

    /**
     * @param Goods $goods
     * @param $images
     * @return array
     */
    public static function parserGoods(Goods $goods)
    {
        /**
         * @var Basket
         */
        $basket = Environment::getContext()->getService('basket');
        /**
         * @var CurrencyBuilder
         */
        $currency = Environment::getContext()->getService('currency');

        $tmp = $goods->as_array();
        unset(
            $tmp['external_id'], $tmp['external_change'], $tmp['value_old'],
            $tmp['amount'], $tmp['pos'], $tmp['status'], $tmp['price_old']
        );
        $tmp['quantity'] = $basket->getQuantity($goods->combination_hash);
        $tmp['value_all'] = $goods->getValue() * $basket->getQuantity($goods->combination_hash);
        $tmp['price_all'] = $currency->format($tmp['value_all'], SYSTEM_CURRENCY);
        $tmp['link'] = $goods->link();
        return $tmp;
    }

    /**
     * @param $configs
     * @return array
     */
    public static function parserConfig($configs)
    {
        if (count($configs) == 0) {
            return array();
        }
        $configs = Environment::countedConfig($configs);
        $result = array();
        foreach ($configs as $conf) {
            if ($conf['status']) {
                unset($conf['type']);
                $result[] = $conf;
            }
        }

        return $result;
    }

    /**
     * @param array $form
     * @param array|false $pos
     * @return Order
     * @throws Error
     */
    public static function checkout($form, $pos)
    {
        /** @var Basket $basket */
        $basket = Environment::getContext()->getService('basket');
        $order = new Order();
        $order->value = $basket->getValueTotal();

        if (Validators::isEmail($form['email'])) {
            $form['email'] = Strings::lower($form['email']);
            $order->email = $form['email'];
        }

        // @note проверяем количество в заказе и количество на складе
        foreach ($basket->getProducts() as $goods) {
            $itemAmount = (float)$goods->amount;
            $basketItemAmount = (float)$basket->getQuantity($goods->combination_hash);
            if ($basketItemAmount > $itemAmount) {
                $order->checkoutError(
                    'amount',
                    array('goods' => $goods)
                );
                
                return $order;
            }
        }

        $order->setConfig($basket->as_array());
        $order->save();

        /** @var Goods $goods */
        foreach ($basket->getProducts() as $goods) {
            $order->addItem(array(
                'item_type' => $basket->getType($goods->combination_hash),
                'item_id' => $goods->combination_hash,
                'article' => $goods->article,
                'goods_id' => $goods->pk(),
                'amount' => $basket->getQuantity($goods->combination_hash),
                'value' => $goods->getValue(),
                'config' => array(
                    'goods' => $goods->as_array()
                ),
            ));
        }

        $i = 0;
        foreach ($form as $code => $field) {
            if ($field) {
                $order->addOption(
                    array(
                        'code' => $code,
                        'value' => $field,
                        'name' => _t('Shop:Store', 'form_' . $code),
                        'pos' => $pos ? $pos[$code] : $i
                    )
                );
                $i++;
            }
        }

        if(Environment::getContext()->getService('browser')->isMobile()){
            $order->addOption(
                array(
                    'code' => 'type',
                    'value' => 'Телефон',
                    'name' => 'Устройство',
                    'pos' =>  $i
                )
            );
        }


        $order->status = ORDER_STATUS_NEW;
        $order->save();
        return $order;
    }

}