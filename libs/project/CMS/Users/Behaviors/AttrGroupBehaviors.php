<?php
namespace CMS\Users\Behaviors;

use CMS\Users\Entity\AttrName;
use CMS\Users\Entity\UserAttr;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;

class AttrGroupBehaviors extends ORMBehavior{

    public function afterDelete(ORM $orm){
        $userAttr = UserAttr::model();
        DB::delete($userAttr->table_name())
            ->where('group_id', '=', $orm->pk())
            ->execute($userAttr->db_config());
        $userAttr->cache_delete();

        $attr = AttrName::model();
        DB::delete($attr->table_name())
            ->where('group_id', '=', $orm->pk())
            ->execute($attr->db_config());
        $attr->cache_delete();
    }

    /**
     * @param $attr
     * @return bool
     */
    public function addAttribute($attr){
        try{
            $ormAttribute = new AttrName($attr[AttrName::model()->primary_key()]);
            $ormAttribute->values($attr);
            $ormAttribute->group_id = $this->getOwner()->pk();
            $ormAttribute->save(true);
            return true;
        }catch (OrmValidationError $e){
            return false;
        }
    }

    /**
     * @param bool $cached
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getAttributes($cached = false){
        $attr = AttrName::model()->whereByGroup($this->getOwner()->pk())->sort();
        if($cached){
            $attr->cached();
        }
        return $attr->find_all();
    }
}