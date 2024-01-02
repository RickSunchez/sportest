<?php
namespace CMS\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use CMS\Catalog\Entity\Category;
use Delorius\DataBase\DB;

class EditObjectForCatalogBehavior extends ORMBehavior
{
    /** @var  bool */
    protected $loaded;
    protected $catId;
    protected $isChanged;
    protected $hasStatus;
    protected $status;
    protected $isChangedStatus;

    /**
     * @param $catId
     * @return Object
     */
    public function whereCatId($catId)
    {
        if (is_array($catId)) {
            $this->getOwner()->where('cid', 'IN', $catId);
        } else {
            $this->getOwner()->where('cid', '=', $catId);
        }

        return $this->getOwner();
    }

    public function afterDelete(ORM $orm)
    {
        if ($orm->has('status')) {
            if ($orm->status) {
                $this->downCountObject($orm->cid);
            }
        } else {
            $this->downCountObject($orm->cid);
        }
    }

    public function beforeSave(ORM $orm)
    {
        $original_values = $orm->original_values();
        $this->catId = $original_values['cid'];
        $this->isChanged = $orm->changed('cid');

        if ($orm->has('status')) {
            $this->hasStatus = true;
            $this->status = $original_values['status'];
            $this->isChangedStatus = $orm->changed('status');
        }

        $this->loaded = $orm->loaded();
    }

    public function afterSave(ORM $orm)
    {
        if (!$this->loaded && $this->isChanged) {

            if ($this->hasStatus) {
                if ($orm->status) {
                    $this->upCountObject($orm->cid);
                }
            } else {
                $this->upCountObject($orm->cid);
            }
        }

        if ($this->loaded) {

            if ($this->hasStatus && $this->isChangedStatus) {
                if ($this->status) {
                    $this->downCountObject($this->catId);
                } else {
                    $this->upCountObject($this->catId);
                }
            }

            if ($this->isChanged) {

                if ($this->hasStatus) {
                    if ($orm->status) {
                        $this->upCountObject($orm->cid);
                        $this->downCountObject($this->catId);
                    }
                } else {
                    $this->upCountObject($orm->cid);
                    $this->downCountObject($this->catId);
                }
            }
        }
    }


    protected function downCountObject($cid)
    {
        $parentCategory = new Category($cid);
        if ($parentCategory->loaded()) {
            $parentCategory->object--;
            $parentCategory->save();
            $parentCategories = $parentCategory->getParents();
            $ormCat = Category::model();
            if (count($parentCategories)) {
                foreach ($parentCategories as $item) {
                    DB::update($ormCat->table_name())
                        ->value('object', $item['object'] - 1)
                        ->where('cid', '=', $item['cid'])
                        ->execute($ormCat->db_config());
                }
            }
            $ormCat->cache_delete();
        }
    }

    protected function upCountObject($cid)
    {
        $newCategory = new Category($cid);
        if ($newCategory->loaded()) {
            $newCategory->object++;
            $newCategory->save();
            $newCategories = $newCategory->getParents();
            $ormCat = Category::model();
            if (count($newCategories)) {
                foreach ($newCategories as $item) {
                    DB::update($ormCat->table_name())
                        ->value('object', $item['object'] + 1)
                        ->where('cid', '=', $item['cid'])
                        ->execute($ormCat->db_config());
                }
            }
            $ormCat->cache_delete();
        }
    }


}