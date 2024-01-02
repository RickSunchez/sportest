<?php

defined('DELORIUS') or die('access denied');


function router_category_url($redirect)
{

    $cat = \Shop\Catalog\Entity\Category::model()->where('owner_id', '=', $redirect['match'][1])->find();

    if ($cat->loaded()) {
        $link = link_to_city('shop_category_list', array('cid' => $cat->pk(), 'url' => $cat->url));
        $response = DI()->getService('httpResponse');
        $response->redirect($link, \Delorius\Http\IResponse::S301_MOVED_PERMANENTLY);
    }


}

function router_product_url($redirect)
{

    $product = \Shop\Commodity\Entity\Goods::model()->where('owner_id', '=', $redirect['match'][1])->find();
    if (!$product->loaded()) {
        $product = \Shop\Commodity\Entity\Goods::model()
            ->order_pk()
            ->where('parent_id', '=', $redirect['match'][1])
            ->find();
        if (!$product->loaded()) {
            return;
        }
    }

    $response = DI()->getService('httpResponse');
    $response->redirect($product->link(), \Delorius\Http\IResponse::S301_MOVED_PERMANENTLY);

}


function is_work()
{
    $h = date('H');
    if ($h >= 10 && $h < 19) {
        return true;
    }
    return false;
}