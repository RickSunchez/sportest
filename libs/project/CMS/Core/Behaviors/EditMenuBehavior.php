<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Config\Menu;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;

class EditMenuBehavior extends ORMBehavior
{
    /** @var  int old parent id */
    protected $catParentId;
    /** @var  bool  */
    protected $loaded;
    /** @var bool  */
    protected $isChanged;

    public function afterDelete(ORM $orm)
    {
        $parentMenu = new Menu($orm->pid);
        if ($parentMenu->loaded()) {
            $parentMenu->children--;
            $parentMenu->save(true);
        }
    }

    public function beforeSave(ORM $orm)
    {
        $original_values = $orm->original_values();
        $this->catParentId = $original_values['pid'];
        $this->isChanged = $orm->changed('pid');
        $this->loaded = $orm->loaded();
    }

    public function afterSave(ORM $orm){

        if ((!$this->loaded || $orm->pid!=0 ) && $this->isChanged ) {
            $newMenu = new Menu($orm->pid);
            if($newMenu->loaded()){
                $newMenu->children++;
                $newMenu->save(true);
            }
        }

        if ($this->loaded && $this->catParentId && $this->isChanged ) {
            $parentMenu = new Menu($this->catParentId);
            if ($parentMenu->loaded()) {
                $parentMenu->children--;
                $parentMenu->save(true);
            }
        }
    }

}