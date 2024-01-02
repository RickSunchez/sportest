<?php

namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\TypeGoods;


class TypeBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(TypeGoods::model()->table_name())
            ->where('goods_id', '=', $orm->pk())
            ->execute(TypeGoods::model()->db_config());
    }

    /**
     * @param $typeId
     * @return Object
     */
    public function whereType($typeId)
    {
        $typeIds = new TypeGoods();
        $this->getOwner()->join($typeIds->table_name(), 'INNER')
            ->on(
                $this->getOwner()->table_name() . '.' . $this->getOwner()->primary_key(),
                '=',
                $typeIds->table_name() . '.goods_id'
            )
            ->where($typeIds->table_name() . '.type_id', '=', $typeId);
        return $this->getOwner();
    }

    /**
     * @return Object
     */
    public function orderByType()
    {
        $typeIds = new TypeGoods();
        $this->getOwner()
            ->order_by($typeIds->table_name() . '.pos', 'desc')
            ->order_by($typeIds->table_name() . '.id', 'desc');
        return $this->getOwner();
    }

    /**
     * @param $typeId
     * @return bool
     */
    public function setType($typeId, $delete = false)
    {
        try {
            $typeGoods = TypeGoods::model()
                ->where('type_id', '=', $typeId)
                ->where('goods_id', '=', $this->getOwner()->pk())
                ->find();
            if ($delete) {
                if ($typeGoods->loaded()) {
                    $typeGoods->delete();
                }
            } else {
                $typeGoods->type_id = $typeId;
                $typeGoods->goods_id = $this->getOwner()->pk();
                $typeGoods->save(true);
            }

            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /**
     * @return array
     */
    public function getTypes()
    {
        $types = TypeGoods::model()->where('goods_id', '=', $this->getOwner()->pk())->find_all();
        return Arrays::resultAsArrayKey($types, 'type_id');
    }

} 