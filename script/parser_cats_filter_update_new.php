<?php
define('DIR_INDEX', __DIR__ . '/../');
include_once DIR_INDEX . '/libs/bootstrap.php';

ignore_user_abort(1);
set_time_limit(0);
ini_set('memory_limit', '-1');


$csv = new \Delorius\Utils\CSV\CSVLoader('p.csv');
$i = $c = 0;

#parser_cats_filter_update.php


foreach ($csv->getItems() as $key => $data) {


    if ($data['t'] == 'p') {

        $id = array_shift($data);
        var_dump($id);
        $type = array_shift($data);
        $name = array_shift($data);

        $product = new \Shop\Commodity\Entity\Goods($id);

        if (!$product->loaded()) {
            continue;
        }


        foreach ($data as $ch => $values) {
            if ($values) {
                $ch_name = \Delorius\Utils\Strings::firstUpper($ch);
                $list = explode(',', $values);
                $tmp_list = array();
                foreach ($list as $key => $value) {
                    $temp = \Delorius\Utils\Strings::firstUpper(\Delorius\Utils\Strings::trim($value));
                    if ($temp)
                        $tmp_list[$key] = $temp;
                }

                $list = $tmp_list;

//                var_dump($ch_name);
//                var_dump($list);
//
//                continue;

                try {
                    $chId = get_chara_id_by_name($ch_name);
                    foreach ($list as $key => $value_name) {
                        $value = get_value($value_name, $chId);

                        if (!is_has_chara($value, $product)) {
                            $product->addCharacteristics(array(
                                'value_id' => $value->pk(),
                                'character_id' => $value->character_id,
                            ));
                        }
                    }
                } catch (\Delorius\Exception\OrmValidationError $e) {
                    var_dump($e->getErrorsMessage());
                    var_dump($e->getErrorsFields());
                    die();
                }


            }
        }


        if ($i == 100 && false) {
            break;
        }

    }


    $i++;
}

/**
 * @param \Shop\Commodity\Entity\CharacteristicsValues $value
 * @param $goodsId
 * @return bool
 * @throws \Delorius\Exception\Error
 */
function is_has_chara(\Shop\Commodity\Entity\CharacteristicsValues $value, \Shop\Commodity\Entity\Goods $product)
{
    return \Shop\Commodity\Entity\CharacteristicsGoods::model()
        ->where('value_id', '=', $value->pk())
        ->where('character_id', '=', $value->character_id)
        ->where('target_id', '=', $product->pk())
        ->where('target_type', '=', \CMS\Core\Helper\Helpers::getTableId($product))
        ->find()
        ->loaded();
}

/**
 * @param $name
 * @param $ch
 * @return \Shop\Commodity\Entity\CharacteristicsValues
 * @throws \Delorius\Exception\Error
 */
function get_value($name, $chId)
{
    $value = \Shop\Commodity\Entity\CharacteristicsValues::model()
        ->where('name', '=', $name)
        ->where('character_id', '=', $chId)->find();

    if (!$value->loaded()) {
        $value->character_id = $chId;
        $value->name = $name;
        $value->save();
    }

    return $value;
}

/**
 * @param $name
 * @return int
 * @throws \Delorius\Exception\Error
 */
function get_chara_id_by_name($name)
{
    $ch = \Shop\Commodity\Entity\Characteristics::model()->where('name', '=', $name)->find();

    if (!$ch->loaded()) {
        $ch->name = $name;
        $ch->save();
    }

    return $ch->pk();
}


var_dump('finish = ' . $i);



