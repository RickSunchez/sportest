<?php

namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\CollectionProductItem;

class CollectionProductBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $items = CollectionProductItem::model()->where('coll_id', '=', $this->getOwner()->pk())->find_all();
        foreach ($items as $item) {
            $item->delete();
        }
    }

    /**
     * @param $value
     * @return bool
     * @throws \Delorius\Exception\Error
     */
    public function addItem($value)
    {
        try {
            $ormItem = new CollectionProductItem($value[CollectionProductItem::model()->primary_key()]);
            if ($value['delete'] == 1) {
                if ($ormItem->loaded()) {
                    $ormItem->delete();
                }
                return true;
            }
            $ormItem->values($value);
            $ormItem->coll_id = $this->getOwner()->pk();
            $ormItem->save(true);
            return $ormItem->pk();
        } catch (OrmValidationError $e) {
            return false;
        }
    }

} 