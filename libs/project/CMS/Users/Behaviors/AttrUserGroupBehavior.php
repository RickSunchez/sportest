<?php
namespace CMS\Users\Behaviors;

use CMS\Users\Entity\UserAttr;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;

class AttrUserGroupBehavior extends ORMBehavior{

    public function afterDelete(ORM $orm){
        $userAttr = UserAttr::model();
        DB::delete($userAttr->table_name())
            ->where('group_id', '=', $orm->group_id)
            ->where('attr_id', '=', $orm->pk())
            ->execute($userAttr->db_config());
        $userAttr->cache_delete();
    }
}