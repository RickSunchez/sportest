<?php
namespace Shop\Store\Component\Cart\Discount;

use Delorius\Tools\Math\Parser;

class PriceDiscount extends BaseDiscount
{

    /**
     * @var \Shop\Store\Component\Cart\ACart
     */
    protected $cart;

    /**
     * @return  bool
     */
    public function valid()
    {
        $result = Parser::build($this->func)->setVars(array('value' => $this->cart->getValueGoods()))->evaluate();
        return $result ? true : false;
    }


    /**
     * This method will be called when the component (or component's parent)
     * becomes attached to a monitored object. Do not call this method yourself.
     * @param  Delorius\ComponentModel\IComponent
     * @return void
     */
    protected function attached($discountBasket)
    {
        if ($discountBasket instanceof \Shop\Store\Component\Cart\DiscountBasket) {
            $this->cart = $discountBasket->cart;
        }
        parent::attached($discountBasket);
    }
} 