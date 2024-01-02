<?php
namespace CMS\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use CMS\Catalog\Entity\Category;

class HelperCategoryBehavior extends ORMBehavior
{
    private $_arrParent = array();

    /**
     * @return array
     */
    public function getParents()
    {
        /** @var \CMS\Catalog\Entity\Category $orm */
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


    private $_arrChildren = array();

    /**
     * @return array
     */
    public function getChildren()
    {
        /** @var \CMS\Catalog\Entity\Category $orm */
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
            $categories = Category::model()->select()->type($type_id)->active()->cached()->find_all();
            foreach ($categories as $cat) {
                $this->_cats[$type_id][$cat['cid']] = $cat;
            }
        }
        return $this->_cats[$type_id];
    }
}