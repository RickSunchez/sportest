<?php

namespace Boat\Store\Component\Cart\Payment;

use Shop\Store\Component\Cart\Payment\PaymentBase;

class PaymentVCredit extends PaymentBase
{

    public function render()
    {
        $goods = $this->cart->getProducts();
        $items = '';
        foreach ($goods as $item) {
            $items .= _sf('<span class="bk_product">
                            <span class="bk_name">{0}</span>
                            <span class="bk_price">{2}</span>
                            <span class="bk_quantity">{1}</span>
                         </span>',
                $item->name,
                $item->amount,
                $item->getPrice(false, false)
            );
        }

        return '
                <div class="l-v-credit">
                <div class="bk_container" partner="179687">
                    <div class="bk_buy_button"
                         onclick="javascript:bk_frame_show(this)">
                        Оформить в кредит
                    </div>
                </div>
                <span class="meta">' . $items . '</span>
                </div>              
                <link rel="stylesheet" type="text/css" href="https://bkred.ru/bkapi/vkredit_green.css" />
                <script src="https://bkred.ru/bkapi/vkredit_raw.js"></script>  
        ';
    }

}