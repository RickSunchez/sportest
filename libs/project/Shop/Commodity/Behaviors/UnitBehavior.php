<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Shop\Commodity\Entity\CharacteristicsValues;
use Shop\Commodity\Entity\Goods;

class UnitBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $goods = Goods::model();
        DB::update($goods->table_name())->set(array('unit_id' => 0))
            ->where('unit_id', '=', $orm->pk())
            ->execute($goods->db_config());
        $goods->cache_delete();

        $chara = CharacteristicsValues::model();
        DB::update($chara->table_name())->set(array('unit_id' => 0))
            ->where('unit_id', '=', $orm->pk())
            ->execute($chara->db_config());
        $chara->cache_delete();
    }

} 