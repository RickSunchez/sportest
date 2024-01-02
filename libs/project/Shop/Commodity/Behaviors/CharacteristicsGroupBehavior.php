<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Shop\Commodity\Entity\Characteristics;

class CharacteristicsGroupBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $chara = Characteristics::model();
        DB::update($chara->table_name())->set(array('group_id'=>0))
            ->where('group_id', '=', $orm->pk())
            ->execute($chara->db_config());
        $chara->cache_delete();
    }

} 