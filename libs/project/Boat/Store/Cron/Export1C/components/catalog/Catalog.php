<?php namespace Boat\Store\Cron\Export1C\components\catalog;

use Shop\Catalog\Entity\Category;

class Catalog
{
    protected $categories = array();

    public function init($importData)
    {
        $groupsExists = (bool)count($importData->Классификатор->Группы->Группа);
        if (!$groupsExists) {
            return $this->categories;
        }

        foreach ($importData->Классификатор->Группы->Группа as $group) {
            $this->importCategories($group);
        }

        return $this->categories;
    }

    protected function importCategories($group, $parentExternalId = 0, $pid = 0)
    {
        if (!$group->Ид) {
            return;
        }

        $id = $group->Ид[0]->__toString();

        $category = array(
            'id' => $id,
            'pid' => $parentExternalId,
            'cid' => 0,
            'name' => '',
        );

        $name = $group->Наименование[0]->__toString();
        $externalId = $group->Ид[0]->__toString();

        if ($externalId) {
            $category['name'] = $name;
            $category['cid'] = $this->defineCategodyId($name, $externalId, $pid);
        }

        $this->categories[$id] = $category;

        if (isset($group->Группы)) {
            foreach ($group->Группы->Группа as $childGroup) {
                $this->importCategories($childGroup, $id, $category['cid']);
            }
        }
    }

    protected function defineCategodyId($categoryName, $externalId, $pid)
    {
        $existsCategory = Category::model()->where('external_id', '=', $externalId)->find();
        if ($existsCategory->loaded()) {
            return $existsCategory->cid;
        }

        $existsCategory = Category::model()->where('name', '=', $categoryName)->find_all();
        if ($existsCategory->count() == 1) {
            foreach ($existsCategory as $cat) {
                $cat->external_id = $externalId;
                $cat->save(true);
                return $cat->cid;
            }
        }

        $existsCategory = Category::model()
            ->where('name', '=', $categoryName)
            ->and_where('pid', '=', $pid)
            ->find_all();
        if ($existsCategory->count() == 1) {
            foreach ($existsCategory as $cat) {
                $cat->external_id = $externalId;
                $cat->save(true);
                return $cat->cid;
            }
        }
        
        return 0;
    }
}
