<?php
namespace CMS\Users\Behaviors;

use CMS\Users\Entity\AttrName;
use CMS\Users\Entity\UserAttr;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;

class AttrUserBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $userAttr = UserAttr::model();
        DB::delete($userAttr->table_name())
            ->where('user_id', '=', $orm->pk())
            ->execute($userAttr->db_config());
        $userAttr->cache_delete();
    }

    /**
     * @param string $code
     * @return mixed
     */
    public function getAttrByCode($code)
    {
        $result = $this->getAttrs(array($code));
        if (count($result) == 0) {
            return '';
        }
        $arr = $result->current();
        return $arr['value'];
    }

    /**
     * @param array|null $codes
     * @return object
     */
    public function getAttrs(array $codes = null)
    {
        $attrName = new AttrName();
        $attrValues = new UserAttr();

        $db = DB::select(
            $attrValues->table_name() . '.id',
            $attrValues->table_name() . '.attr_id',
            $attrValues->table_name() . '.group_id',
            $attrName->table_name() . '.name',
            $attrName->table_name() . '.code',
            $attrName->table_name() . '.active',
            $attrName->table_name() . '.require',
            $attrValues->table_name() . '.value'
        )
            ->from($attrValues->table_name())
            ->join($attrName->table_name(), 'INNER')
            ->on($attrValues->table_name() . '.attr_id', '=', $attrName->table_name() . '.id')
            ->where($attrValues->table_name() . '.user_id', '=', $this->getOwner()->pk())
            ->order_by($attrName->table_name() . '.pos')
            ->order_by($attrName->table_name() . '.' . $attrName->primary_key());

        if (count($codes)) {
            $db->where($attrName->table_name() . '.code', 'IN', $codes);
        }
        $result = $db->execute($attrValues->db_config());
        return $result;
    }


    /**
     * @param $value
     * @return bool
     */
    public function addAttr($value)
    {
        try {
            $ormSection = new UserAttr($value[UserAttr::model()->primary_key()]);
            if (
                $ormSection->loaded() &&
                empty($value['value'])
            ) {
                $ormSection->delete(true);
                return true;
            }
            $ormSection->values($value);
            $ormSection->user_id = $this->getOwner()->pk();
            $ormSection->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }
}