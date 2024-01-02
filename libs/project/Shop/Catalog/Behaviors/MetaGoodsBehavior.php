<?php

namespace Shop\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Shop\Catalog\Entity\CategoryMetaGoods;

class MetaGoodsBehavior extends ORMBehavior
{

    /**
     * @return CategoryMetaGoods
     */
    public function getMetaGoods()
    {
        $pk = $this->getOwner()->pk();
        $metaGoods = CategoryMetaGoods::model()->where('cid', '=', $pk)->find();
        if ($metaGoods->loaded()) {
            return $metaGoods;
        } else {
            $metaGoods = new CategoryMetaGoods();
            $metaGoods->cid = $pk;
            return $metaGoods;
        }
    }

} 