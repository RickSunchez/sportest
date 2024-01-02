<?php
namespace Location\Store\Behaviors;


class CollectionBehavior extends \Shop\Commodity\Behaviors\CollectionBehavior
{

    /**
     * @return string
     */
    public function link()
    {
        return link_to_city('shop_collection', array('url' => $this->getOwner()->url, 'id' => $this->getOwner()->pk()));
    }



} 