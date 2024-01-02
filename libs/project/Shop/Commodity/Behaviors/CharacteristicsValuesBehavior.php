<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Shop\Commodity\Entity\CharacteristicsGoods;

class CharacteristicsValuesBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(CharacteristicsGoods::model()->table_name())
            ->where('character_id', '=', $orm->pk())
            ->execute(CharacteristicsGoods::model()->db_config());

    }

} 