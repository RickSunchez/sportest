<?php

namespace Shop\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Shop\Catalog\Entity\Category;

class EditGoodsForCatalogBehavior extends ORMBehavior
{
    /** @var  bool */
    protected $loaded;
    protected $catId;
    protected $isChanged;
    protected $status;
    protected $isChangedStatus;

    /**
     * @param $catId
     * @return Object
     */
    public function whereCatId($catId)
    {
        $table = $this->getOwner()->table_name();
        if ($this->getOwner()->isMulti()) {
            $this->getOwner()->whereCatsId($catId);
        } else {
            if (is_array($catId)) {
                $this->getOwner()->where($table . '.cid', 'IN', $catId);
            } else {
                $this->getOwner()->where($table . '.cid', '=', $catId);
            }
        }

        return $this->getOwner();
    }

    public function afterDelete(ORM $orm)
    {
        if ($orm->status)
            $this->downCountGoods($orm->cid);

    }

    public function beforeSave(ORM $orm)
    {
        $original_values = $orm->original_values();
        $this->catId = $original_values['cid'];
        $this->isChanged = $orm->changed('cid');
        $this->status = $original_values['status'];
        $this->isChangedStatus = $orm->changed('status');
        $this->loaded = $orm->loaded();
    }

    public function afterSave(ORM $orm)
    {

        if (!$this->loaded && $orm->status && $this->isChanged) {

            $this->upCountGoods($orm->cid);

        }

        if ($this->loaded) {

            if ($this->isChangedStatus) {
                if ($this->status) {
                    $this->downCountGoods($this->catId);
                } else {
                    $this->upCountGoods($this->catId);
                }
            }

            if ($this->isChanged) {

                if ($orm->status) {
                    $this->upCountGoods($orm->cid);
                    $this->downCountGoods($this->catId);
                }

            }
        }

    }


    protected function downCountGoods($cid)
    {
        $parentCategory = new Category($cid);
        if ($parentCategory->loaded()) {
            $parentCategory->goods--;
            $parentCategory->save();
            $parentCategories = $parentCategory->getParents();
            $ormCat = Category::model();
            if (count($parentCategories)) {
                foreach ($parentCategories as $item) {
                    DB::update($ormCat->table_name())
                        ->value('goods', $item['goods'] - 1)
                        ->where('cid', '=', $item['cid'])
                        ->execute($ormCat->db_config());
                }
            }
            $ormCat->cache_delete();
        }
    }

    protected function upCountGoods($cid)
    {
        $newCategory = new Category($cid);
        if ($newCategory->loaded()) {
            $newCategory->goods++;
            $newCategory->save();
            $newCategories = $newCategory->getParents();
            $ormCat = Category::model();
            if (count($newCategories)) {
                foreach ($newCategories as $item) {
                    DB::update($ormCat->table_name())
                        ->value('goods', $item['goods'] + 1)
                        ->where('cid', '=', $item['cid'])
                        ->execute($ormCat->db_config());
                }
            }
            $ormCat->cache_delete();
        }
    }


}