<?php

namespace Shop\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\CategoryMulti;


class MultiCategoryBehavior extends ORMBehavior
{
    public function afterDelete(ORM $orm)
    {
        DB::delete(CategoryMulti::model()->table_name())
            ->where('product_id', '=', $orm->pk())
            ->execute(CategoryMulti::model()->db_config());
    }

    public function setCat($value)
    {
        try {
            $orm = new CategoryMulti($value[CategoryMulti::model()->primary_key()]);
            if ($value['delete'] == 1 || $value['cid'] == 0) {
                if ($orm->loaded()) {
                    $orm->delete();
                }
                return true;
            }
            if (!$orm->loaded()) {
                $isset = CategoryMulti::model()
                    ->where('cid', '=', $value['cid'])
                    ->where('product_id', '=', $this->getOwner()->pk())
                    ->find()
                    ->loaded();

                if ($isset) {
                    return false;
                }
            }
            $orm->values($value);
            $orm->product_id = $this->getOwner()->pk();
            $orm->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    public function getCatIds()
    {
        $cats = CategoryMulti::model()->select()->where('product_id', '=', $this->getOwner()->pk())->find_all();
        return $cats;
    }

    public function getMultiCategories()
    {
        $multi = CategoryMulti::model();
        $table = Category::model();

        $categories = Category::model()
            ->join($multi->table_name(), 'inner')
            ->on($table->table_name() . '.cid', '=', $multi->table_name() . '.cid')
            ->where($multi->table_name() . '.product_id', '=', $this->getOwner()->pk())
            ->active()
            ->find_all();

        return $categories;
    }


    public function whereCatsId($ids)
    {
        $orm = $this->getOwner();
        $table = CategoryMulti::model();

        $orm->join($table->table_name(), 'inner')
            ->on($table->table_name() . '.product_id', '=', $orm->table_name() . '.' . $orm->primary_key())
            ->where($table->table_name() . '.cid', 'in', $this->getIdsTable($ids))
            ->group_by($orm->table_name() . '.' . $orm->primary_key());

        return $orm;
    }

    protected function getIdsTable($ids)
    {
        if (is_array($ids)) {
            return $ids;
        } else {
            return array($ids);
        }
    }

}