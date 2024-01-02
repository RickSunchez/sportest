<?php

namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Page;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;

class EditPageBehavior extends ORMBehavior
{
    /** @var  int old parent id */
    protected $catParentId;
    /** @var  bool */
    protected $loaded;
    /** @var bool */
    protected $isChanged;
    /** @var bool */
    protected $delete;
    /** @var null */
    protected $parentCategory = null;

    public function afterDelete(ORM $orm)
    {
        $parentCategory = new Page($orm->pid);
        if ($parentCategory->loaded()) {
            $parentCategory->children--;
            $parentCategory->save(true);
        }
    }

    public function beforeSave(ORM $orm)
    {
        $original_values = $orm->original_values();
        $this->catParentId = $original_values['pid'];
        $this->isChanged = $orm->changed('pid');
        $this->loaded = $orm->loaded();
        $this->delete = false;

        if ($this->loaded && $this->catParentId && $this->isChanged) {

            if ($orm->pid) {
                $newORM = new Page($orm->pid);

                if (!$newORM->loaded()) {
                    $orm->pid = $this->catParentId;
                    return;
                }

                if ($newORM->site !== $orm->site) {
                    $orm->pid = $this->catParentId;
                    return;
                }
            }

            $this->parentCategory = new Page($this->catParentId);

            if (!$this->parentCategory->loaded()) {
                return;
            }

            if ($this->parentCategory->site !== $orm->site) { #is_site
                $orm->pid = $this->catParentId;
                return;
            }

            if ($this->parentCategory->children) { #is_child
                if (!$this->_checkPageParent($orm->pk(), $orm->pid)) {
                    $orm->pid = $this->catParentId;
                    return;
                }
            }

            $this->delete = true;
        }

    }

    public function afterSave(ORM $orm)
    {

        if ((!$this->loaded || $orm->pid != 0) && $this->isChanged) {
            $newCategory = new Page($orm->pid);
            if ($newCategory->loaded()) {
                $newCategory->children++;
                $newCategory->save(true);
            }
        }

        if (
            $this->loaded &&
            $this->catParentId &&
            $this->isChanged &&
            $this->delete &&
            $this->parentCategory
        ) {
            $this->parentCategory->children--;
            $this->parentCategory->save(true);
        }

    }

    protected $_arrChildrenPage = array();

    protected function _checkPageParent($oldId, $newIid)
    {
        $result = $this->_getPage();
        $this->_setChildren($result, $oldId);
        foreach ($this->_arrChildrenPage as $page) {
            if ($page['id'] == $newIid) {
                return false;
            }
        }
        return true;
    }

    protected function _setChildren($result, $pid = 0)
    {
        if (sizeof($result[$pid])) {
            foreach ($result[$pid] as $page) {
                $this->_arrChildrenPage[] = $page;
                $this->_setChildren($result, $page['id']);
            }
        }
    }

    protected function _getPage()
    {
        $pages = Page::model()
            ->site($this->getOwner()->site)
            ->select('pid', 'id')
            ->cached()
            ->find_all();
        $arr = array();
        foreach ($pages as $page) {
            $arr[$page['pid']][] = $page;
        }
        return $arr;
    }

}