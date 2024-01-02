<?php

namespace Shop\Commodity\Behaviors;

use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Characteristics;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\CharacteristicsGroup;
use Shop\Commodity\Entity\CharacteristicsValues;
use Shop\Commodity\Entity\Unit;

class GoodsCharacteristicsBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(CharacteristicsGoods::model()->table_name())
            ->where('target_id', '=', $orm->pk())
            ->where('target_type', '=', Helpers::getTableId($orm))
            ->execute(CharacteristicsGoods::model()->db_config());
        CharacteristicsGoods::model()->cache_delete();
    }

    /** @return \Delorius\DataBase\Result */
    public function getValueCharacteristics()
    {
        $value = CharacteristicsGoods::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->sort()
            ->cached();

        return $value->find_all();
    }

    protected static $units = null;
    protected static $values = null;
    protected static $charas = null;
    protected static $groups = null;

    /** @return array */
    public function getCharacteristics()
    {

        /** values */
        if (!self::$units) {
            self::$units = Arrays::resultAsArrayKey(
                Unit::model()
                    ->select()
                    ->cached()
                    ->find_all(),
                'unit_id');
        }
        if (!self::$values) {
            $ormValues = CharacteristicsValues::model()->select()->cached()->find_all();
            foreach ($ormValues as $item) {
                $item['unit'] = isset(self::$units[$item['unit_id']]) ? self::$units[$item['unit_id']]['abbr'] : '';
                self::$values[$item['value_id']] = $item;
            }
        }
        if (!self::$charas) {
            /** characteristics */
            $ormChara = Characteristics::model()->cached()->select()->find_all();
            self::$charas = Arrays::resultAsArrayKey($ormChara, 'character_id', true);
        }

        $charaGoods = CharacteristicsGoods::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->sort()
            ->find_all();

        /** @var $goods CharacteristicsGoods */
        $resultGoods = array();
        foreach ($charaGoods as $goods) {
            $resultGoods[$goods->character_id][] = array(
                'chara' => self::$charas[$goods->character_id],
                'value' => self::$values[$goods->value_id]
            );

        }
        return $resultGoods;
    }


    /**
     * @param bool $main
     * @param bool $is_group
     * @return array|ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    public function getGroupCharacteristics($main = false, $is_group = false)
    {

        /** values */
        if (!self::$units) {
            self::$units = Arrays::resultAsArrayKey(
                Unit::model()
                    ->select()
                    ->cached()
                    ->find_all(),
                'unit_id');
        }

        if (!self::$values) {
            $ormValues = CharacteristicsValues::model()->select()->cached()->find_all();
            foreach ($ormValues as $item) {
                $item['unit'] = isset(self::$units[$item['unit_id']]) ? self::$units[$item['unit_id']]['abbr'] : '';
                $item['unit_name'] = isset(self::$units[$item['unit_id']]) ? self::$units[$item['unit_id']]['name'] : '';
                self::$values[$item['value_id']] = $item;
            }
        }
        if (!self::$charas) {
            /** characteristics */
            $ormChara = Characteristics::model()->cached()->select()->find_all();
            self::$charas = Arrays::resultAsArrayKey($ormChara, 'character_id', true);
        }


        $charaGoods = CharacteristicsGoods::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->sort();
        if ($main) {
            $charaGoods->where('main', '=', 1);
        }

        $result = $charaGoods->find_all();
        $resultGoods = array();
        /** @var $goods CharacteristicsGoods */
        foreach ($result as $goods) {
            $this->initCharacteristicsGoods($resultGoods, $goods);
        }

        if (!$is_group) {
            return $resultGoods;
        }

        $groups = CharacteristicsGroup::model()->cached()->sort()->find_all();

        $result = array();
        foreach ($groups as $group) {
            if (isset($resultGoods[$group->pk()])) {
                $result[] = array(
                    'group' => $group->as_array(),
                    'values' => $resultGoods[$group->pk()]
                );
            }
        }

        return sizeof($result) ? $result : $resultGoods;
    }

    public function addCharacteristics($value)
    {
        try {
            $ormChara = new CharacteristicsGoods($value['id']);
            if ($value['delete'] == 1) {
                if ($ormChara->loaded()) {
                    $ormChara->delete(true);
                }
                return true;
            }
            $ormChara->values($value);
            $ormChara->target_id = $this->getOwner()->pk();
            $ormChara->target_type = Helpers::getTableId($this->getOwner());
            $ormChara->save(true);
            return true;
        } catch (OrmValidationError $e) {
            $er = $e->getErrorsMessage();

            return false;
        }
    }

    protected function initCharacteristicsGoods(& $resultGoods, $goods)
    {
        $group_id = self::$charas[$goods->character_id]['group_id'];

        if (!count($resultGoods[$group_id])) {
            $resultGoods[$group_id][] = array(
                'chara' => self::$charas[$goods->character_id],
                'value' => self::$values[$goods->value_id]
            );
        } else {

            $isset = false;
            foreach ($resultGoods[$group_id] as $index => $item) {

                if ($item['chara']['character_id'] == $goods->character_id) {

                    if (isset($resultGoods[$group_id][$index]['value'])) {
                        $value = $resultGoods[$group_id][$index]['value'];
                        unset($resultGoods[$group_id][$index]['value']);
                        $resultGoods[$group_id][$index]['values'][] = $value;
                    }
                    $resultGoods[$group_id][$index]['values'][] = self::$values[$goods->value_id];
                    $isset = true;
                    break;
                }
            }

            if (!$isset) {
                $resultGoods[$group_id][] = array(
                    'chara' => self::$charas[$goods->character_id],
                    'value' => self::$values[$goods->value_id]
                );
            }
        }
    }

}