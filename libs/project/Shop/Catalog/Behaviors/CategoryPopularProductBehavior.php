<?php

namespace Shop\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Catalog\Entity\CategoryPopularProduct;

class CategoryPopularProductBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(CategoryPopularProduct::model()->table_name())
            ->where('cat_id', '=', $orm->pk())
            ->execute(CategoryPopularProduct::model()->db_config());
    }

    /** @return \Delorius\DataBase\Result */
    public function getProductIds()
    {
        $ids = CategoryPopularProduct::model()
            ->where('cat_id', '=', $this->getOwner()->pk())
            ->select('product_id')
            ->sort();
        return $ids->find_all();
    }

    public function addProduct($values)
    {
        try {
            $item = CategoryPopularProduct::model()
                ->where('cat_id', '=', $this->getOwner()->pk())
                ->where('product_id', '=', $values['product_id'])
                ->find();

            if ($values['delete'] == 1) {
                if ($item->loaded()) {
                    $item->delete();
                }
                return true;
            }
            $item->values($values);
            $item->cat_id = $this->getOwner()->pk();
            $item->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

} 