<?php

namespace Shop\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Helpers\Shop;

class HelperCategoryBehavior extends ORMBehavior
{
    private $_arrParent = array();
    private $_set_active = true;

    public function set_active_cats($bool = true)
    {
        $this->_set_active = $bool;
    }

    /**
     * @return array
     */
    public function getParents()
    {
        /** @var \Shop\Catalog\Entity\Category $orm */
        $orm = $this->getOwner();
        if (!$orm->loaded()) {
            return array();
        }
        if (!$orm->pid) {
            return array();
        }

        if (!count($this->_arrParent[$orm->pk()])) {
            $arr = $this->getCategories();
            $this->_arrParent[$orm->pk()] = array();
            $parentId = $orm->pid;
            while ($category = $arr[$parentId]) {
                $this->_arrParent[$orm->pk()][] = $category;
                $parentId = $category['pid'];
            }
        }
        return $this->_arrParent[$orm->pk()];
    }

    /**
     * @return mixed
     */
    public function getFirstParent()
    {
        $parent = $this->getOwner()->as_array();
        foreach ($this->getParents() as $item) {
            $parent = $item;
        }
        return $parent;
    }


    private $_arrChildren = array();

    /**
     * @return array
     */
    public function getChildren()
    {
        /** @var \Shop\Catalog\Entity\Category $orm */
        $orm = $this->getOwner();
        if (!$orm->loaded()) {
            return array();
        }
        if (!$orm->children) {
            return array();
        }

        if (!count($this->_arrChildren[$orm->pk()])) {
            $result = $this->getPidCat();
            $this->setChildren($result, $orm->pk());
        }
        return $this->_arrChildren[$orm->pk()];
    }

    private $_pidCats = array();

    protected function getPidCat()
    {
        if (!count($this->_pidCats)) {
            $arr = $this->getCategories();
            foreach ($arr as $cat) {
                $this->_pidCats[$cat['pid']][] = $cat;
            }
        }
        return $this->_pidCats;
    }

    protected function setChildren($result, $pid = 0)
    {
        if (sizeof($result[$pid])) {
            foreach ($result[$pid] as $cat) {
                $this->_arrChildren[$this->getOwner()->pk()][] = $cat;
                $this->setChildren($result, $cat['cid']);
            }
        }
    }

    private $_cats = array();

    protected final function getCategories()
    {
        $type_id = $this->getOwner()->type_id;
        if (!count($this->_cats[$type_id])) {
            $categories = Category::model()->select()->type($type_id)->cached();

            if ($this->_set_active) {
                $categories->active();
            }

            $res = $categories->find_all();
            foreach ($res as $cat) {
                $this->_cats[$type_id][$cat['cid']] = $cat;
            }
        }
        return $this->_cats[$type_id];
    }


    /**
     * @param string $glue
     * @return mixed|string
     */
    public function getCategoryStr($glue = '/')
    {
        return Shop::getCategoriesListStr($glue, $this->getOwner()->pk(), $this->getOwner()->type_id);
    }

}