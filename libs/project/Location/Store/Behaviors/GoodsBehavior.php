<?php
namespace Location\Store\Behaviors;


class GoodsBehavior extends \Shop\Commodity\Behaviors\GoodsBehavior
{

    /**
     * @return string
     */
    public function link()
    {
        return link_to_city('shop_goods', array('url' => $this->getOwner()->url, 'id' => $this->getOwner()->pk()));
    }



} 