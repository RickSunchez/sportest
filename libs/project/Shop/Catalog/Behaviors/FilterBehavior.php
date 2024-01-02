<?php
namespace Shop\Catalog\Behaviors;

use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Catalog\Entity\Filter;


class FilterBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $filter = Filter::model();
        DB::delete($filter->table_name())
            ->where('target_id', '=', $orm->pk())
            ->where('target_type', '=', Helpers::getTableId($orm))
            ->execute($filter->db_config());
        $filter->cache_delete();
    }

    /**
     * @param array $values
     * @return bool
     */
    public function addFilter($values)
    {
        try {
            $ormSection = new Filter($values[Filter::model()->primary_key()]);
            $ormSection->values($values);
            $ormSection->target_id = $this->getOwner()->pk();
            $ormSection->target_type = Helpers::getTableId($this->getOwner());
            $ormSection->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /**
     * @return \Delorius\DataBase\Result
     */
    public function getFilters()
    {
        $types = Filter::model()
            ->select()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->sort()
            ->select()
            ->cached()
            ->find_all();
        return $types;
    }

} 