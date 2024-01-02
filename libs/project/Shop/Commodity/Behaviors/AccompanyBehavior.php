<?php
namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\Accompany;
use Shop\Commodity\Entity\Goods;

class AccompanyBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(Accompany::model()->table_name())
            ->where('current_id', '=', $orm->pk())
            ->execute(Accompany::model()->db_config());
    }

    /**
     * @param int $goodsId Goods Id Accompany
     * @return bool
     */
    public function setAccompany($goodsId, $delete = false)
    {
        try {
            $acco = Accompany::model()
                ->where('target_id', '=', $goodsId)
                ->where('current_id', '=', $this->getOwner()->pk())
                ->find();
            if ($delete) {
                if ($acco->loaded()) {
                    $acco->delete(true);
                }
                return true;
            }

            $acco->target_id = $goodsId;
            $acco->current_id = $this->getOwner()->pk();
            $acco->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /**
     * @return ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    public function getAccompanies($type_id = null, $cache = false)
    {
        $accoIds = new Accompany();
        $goods = new Goods();
        $goods
            ->join($accoIds->table_name(), 'INNER')
            ->on(
                $goods->table_name() . '.' . $goods->primary_key(),
                '=',
                $accoIds->table_name() . '.target_id'
            )
            ->where($accoIds->table_name() . '.current_id', '=', $this->getOwner()->pk())
            ->active()
            ->order_by($accoIds->table_name() . '.type_id')
            ->order_by($accoIds->table_name() . '.pos', 'desc')
            ->order_pk();

        if ($type_id != null) {
            $goods->where($accoIds->table_name() . '.type_id', '=', $type_id);
        }

        if ($cache) {
            $goods->cached();
        }
        return $goods->find_all();
    }

} 