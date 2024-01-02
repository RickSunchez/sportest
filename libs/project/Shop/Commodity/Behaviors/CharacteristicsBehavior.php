<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\CharacteristicsValues;

class CharacteristicsBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(CharacteristicsValues::model()->table_name())
            ->where('character_id', '=', $orm->pk())
            ->execute(CharacteristicsValues::model()->db_config());
        DB::delete(CharacteristicsGoods::model()->table_name())
            ->where('character_id', '=', $orm->pk())
            ->execute(CharacteristicsGoods::model()->db_config());
    }

    /** @return \Delorius\DataBase\Result */
    public function getValues()
    {
        $value = CharacteristicsValues::model()
            ->where('character_id', '=', $this->getOwner()->pk())
            ->sort();

        return $value->find_all();
    }

    public function addValue($value)
    {
        try {
            $ormChara = new CharacteristicsValues($value['value_id']);
            $ormChara->values($value);
            $ormChara->character_id = $this->getOwner()->pk();
            $ormChara->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

} 