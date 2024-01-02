<?php

namespace Shop\Commodity\Behaviors;

use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\Attribute;

class AttributeBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(Attribute::model()->table_name())
            ->where('target_id', '=', $orm->pk())
            ->where('target_type', '=', Helpers::getTableId($orm))
            ->execute(Attribute::model()->db_config());
    }

    /** @return \Delorius\DataBase\Result */
    public function getAttributes()
    {
        $attributes = Attribute::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->sort();
        return $attributes->find_all();
    }

    public function addAttribute($attribute)
    {
        try {
            $ormAttribute = new Attribute($attribute[Attribute::model()->primary_key()]);
            if ($attribute['delete'] == 1) {
                if ($ormAttribute->loaded()) {
                    $ormAttribute->delete();
                }
                return true;
            }
            $ormAttribute->values($attribute);
            $ormAttribute->target_id = $this->getOwner()->pk();
            $ormAttribute->target_type = Helpers::getTableId($this->getOwner());
            $ormAttribute->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

} 