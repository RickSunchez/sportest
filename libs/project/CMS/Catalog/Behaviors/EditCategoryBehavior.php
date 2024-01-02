<?php
namespace CMS\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use CMS\Catalog\Entity\Category;
use Delorius\DataBase\DB;

class EditCategoryBehavior extends ORMBehavior
{
    /** @var  int old parent id */
    protected $catParentId;
    /** @var  bool */
    protected $loaded;
    /** @var bool */
    protected $isChanged;

    public function afterDelete(ORM $orm)
    {
        $parentCategory = new Category($orm->pid);
        if ($parentCategory->loaded()) {
            $parentCategory->children--;
            $parentCategory->object -= $orm->object;
            $parentCategory->save(true);
            $newCategories = $parentCategory->getParents();
            if (count($newCategories)) {
                $ormCat = Category::model();
                foreach ($newCategories as $item) {
                    DB::update($ormCat->table_name())
                        ->value('object', $item['object'] - $orm->object)
                        ->where('cid', '=', $item['cid'])
                        ->execute($ormCat->db_config());
                }
            }
        }
    }

    public function beforeSave(ORM $orm)
    {
        $original_values = $orm->original_values();
        $this->catParentId = $original_values['pid'];
        $this->isChanged = $orm->changed('pid');
        $this->loaded = $orm->loaded();
    }

    public function afterSave(ORM $orm)
    {
        if ((!$this->loaded || $orm->pid != 0) && $this->isChanged) {
            $newCategory = new Category($orm->pid);
            if ($newCategory->loaded()) {
                $newCategory->children++;
                $newCategory->object += $orm->object;
                $newCategory->save();
                $newCategories = $newCategory->getParents();
                if (count($newCategories)) {
                    $ormCat = Category::model();
                    foreach ($newCategories as $item) {
                        DB::update($ormCat->table_name())
                            ->value('object', $item['object'] + $orm->object)
                            ->where('cid', '=', $item['cid'])
                            ->execute($ormCat->db_config());
                    }
                }
            }
        }

        if ($this->loaded && $this->catParentId && $this->isChanged) {
            $parentCategory = new Category($this->catParentId);
            if ($parentCategory->loaded()) {
                $parentCategory->children--;
                $parentCategory->object -= $orm->object;
                $parentCategory->save(true);
                $newCategories = $parentCategory->getParents();
                if (count($newCategories)) {
                    $ormCat = Category::model();
                    foreach ($newCategories as $item) {
                        DB::update($ormCat->table_name())
                            ->value('object', $item['object'] - $orm->object)
                            ->where('cid', '=', $item['cid'])
                            ->execute($ormCat->db_config());
                    }
                }
            }
        }
    }


}