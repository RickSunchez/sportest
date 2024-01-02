<?php

defined('DELORIUS') or die('access denied');

function product_cat($cid)
{
    static $categories = null;
    if (!$categories) {
        $orm = \Shop\Catalog\Entity\Category::model()
            ->type(\Shop\Catalog\Entity\Category::TYPE_GOODS)
            ->cached()
            ->select('cid', 'name')
            ->active()
            ->find_all();
        $categories = \Delorius\Utils\Arrays::resultAsArrayKey($orm, 'cid');
    }

    if (isset($categories[$cid])) {
        return $categories[$cid]['name'];
    }

    return '';
}

function product_vendor($id)
{
    static $vendors = null;
    if (!$vendors) {
        $orm = \Shop\Commodity\Entity\Vendor::model()
            ->cached()
            ->select('vendor_id', 'name')
            ->find_all();
        $vendors = \Delorius\Utils\Arrays::resultAsArrayKey($orm, 'vendor_id');
    }

    if (isset($vendors[$id])) {
        return $vendors[$id]['name'];
    }

    return '';
}