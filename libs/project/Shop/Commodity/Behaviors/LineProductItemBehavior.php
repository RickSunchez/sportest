<?php

namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\LineProductItem;

class LineProductItemBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(LineProductItem::model()->table_name())
            ->where('line_id', '=', $orm->pk())
            ->execute(LineProductItem::model()->db_config());
    }

    /** @return \Delorius\DataBase\Result */
    public function getProductIds()
    {
        $ids = LineProductItem::model()
            ->where('line_id', '=', $this->getOwner()->pk())
            ->select('product_id')
            ->sort();
        return $ids->find_all();
    }

    public function addProduct($values)
    {
        try {
            $item = LineProductItem::model()
                ->where('line_id', '=', $this->getOwner()->pk())
                ->where('product_id', '=', $values['product_id'])
                ->find();

            if ($values['delete'] == 1) {
                if ($item->loaded()) {
                    $item->delete();
                }
                return true;
            }
            $item->values($values);
            $item->line_id = $this->getOwner()->pk();
            $item->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

} 