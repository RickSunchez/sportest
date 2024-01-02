<?php

namespace Shop\Catalog\Helpers;

use CMS\Core\Helper\Helpers;
use Delorius\Core\Environment;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\Goods;

class Filter
{

    public static function getGoodsIds($get = null)
    {
        if (!$get) {
            return array();
        }

        /** @var \Shop\Store\Model\CurrencyBuilder $currency */
        $currency = Environment::getContext()->getService('currency');
        $goodsTypeId = Environment::getContext()->getService('site')->goodsTypeId;

        $orm = Goods::model()
            ->select('goods_id')
            ->active();

        $orm->where($orm->table_name() . '.ctype', '=', $goodsTypeId);

        if (count($get['vendors'])) {
            $ids = array();
            foreach ($get['vendors'] as $vendor) {
                $ids[] = (int)$vendor;
            }
            $orm->where($orm->table_name() . '.vendor_id', 'in', $ids);
        }

        if (count($get['cats']) && !$orm->isMulti()) {
            $ids = array();
            foreach ($get['cats'] as $cat) {
                $ids[] = (int)$cat;
            }
            $orm->where($orm->table_name() . '.cid', 'in', $ids);
        }

        if (isset($get['price_max'])) {
            $value_max = $currency->convert($get['price_max'], $currency->getCode(), SYSTEM_CURRENCY);
            $orm->where($orm->table_name() . '.value_system', '<=', $value_max);
        }

        if (isset($get['price_min'])) {
            $value_min = $currency->convert($get['price_min'], $currency->getCode(), SYSTEM_CURRENCY);
            $orm->where($orm->table_name() . '.value_system', '>=', $value_min);
        }

        //feature
        if (count($get['feature'])) {
            $chara_goods = CharacteristicsGoods::model()
                ->where('target_type', '=', Helpers::getTableId($orm))
                ->cached();

            if (count($get['feature'])) {
                $chara_goods->where_open();
                foreach ($get['feature'] as $feature_id => $value) {
                    if (is_array($value)) {
                        $chara_goods->or_where_open();
                        $chara_goods->where('character_id', '=', (int)$feature_id);
                        $chara_goods->where_open();
                        foreach ($value as $key => $id) {
                            if ((int)$id) {
                                $chara_goods->or_where('value_id', '=', (int)$id);
                            }
                        }
                        $chara_goods->where_close();
                        $chara_goods->or_where_close();

                    } else {
                        if ((int)$value) {
                            $chara_goods->or_where_open()
                                ->where('character_id', '=', (int)$feature_id)
                                ->where('value_id', '=', (int)$value)
                                ->or_where_close();
                        }
                    }
                }
                $chara_goods->where_close();
            }

            $result = $chara_goods->find_all();
            $arr = array();
            foreach ($result as $item) {
                $arr[$item->target_id][$item->character_id] = $item->value_id;
            }

            $goodsIds = array();
            foreach ($arr as $id => $item) {
                if (count($item) == count($get['feature'])) {
                    if ($get['goods']) {
                        $goodsIds[$id] = $id;
                    } else {
                        $goodsIds[] = $id;
                    }

                }
            }

            if ($get['goods']) {
                $ids = array();
                foreach ($get['goods'] as $id) {
                    if (isset($goodsIds[$id])) {
                        $ids[] = $id;
                    }
                }
                $goodsIds = $ids;
            }


            if (count($goodsIds)) {
                $orm->where($orm->table_name() . '.goods_id', 'in', $goodsIds);
            } else {
                $orm->where($orm->table_name() . '.goods_id', '=', 0);
            }
        } else {
            if ($get['goods']) {
                $orm->where($orm->table_name() . '.goods_id', 'in', $get['goods']);
            }
        }

        $goods = $orm->find_all();
        $ids = array();
        foreach ($goods as $item) {
            $ids[] = $item['goods_id'];
        }
        return $ids;
    }

    /**
     * @param $features_hash
     * @return array GET
     */
    public static function parser_hash($features_hash)
    {
        if ($features_hash == '') return array();

        $features = explode('_', $features_hash);
        if (0 == count($features)) return array();

        $get = array();
        foreach ($features as $feature) {
            $arr = explode('-', $feature);

            $first = array_shift($arr);

            if ($first == 'c') { //cats
                if (count($arr)) {
                    sort($arr, SORT_NUMERIC);
                    foreach ($arr as $v) {
                        $get['cats'][$v] = $v;
                    }
                }
            }

            if ((int)$first != 0) { //features
                if (count($arr)) {
                    sort($arr, SORT_NUMERIC);
                    foreach ($arr as $f) {
                        $get['feature'][$first][$f] = $f;
                    }
                }
            }

            if ($first == 'p') { //price
                if ($arr[0] != 0) {
                    $get['price_min'] = $arr[0];
                }
                if ($arr[1] != 0) {
                    $get['price_max'] = $arr[1];
                }
            }

            if ($first == 'v') { //vendors
                if (count($arr)) {
                    sort($arr, SORT_NUMERIC);
                    foreach ($arr as $v) {
                        $get['vendors'][$v] = $v;
                    }
                }
            }

        }

        return $get;
    }

    /**
     * @param array $features_get
     * @return string features_hash
     */
    public static function parser_get($get)
    {
        if (0 == count($get)) return '';

        $hash = array();

        if (isset($get['cats']) && count($get['cats'])) { // cats
            $ids = array();
            foreach ($get['cats'] as $id) {
                $ids[] = $id;
            }
            sort($ids, SORT_NUMERIC);
            $hash[] = 'c-' . implode('-', $ids);
        }

        if (isset($get['feature']) && count($get['feature'])) { //feature
            ksort($get['feature'], SORT_NUMERIC);
            foreach ($get['feature'] as $fid => $data) {
                sort($get['feature'][$fid], SORT_NUMERIC);
                $ids = array();
                foreach ($get['feature'][$fid] as $id) {
                    $ids[] = $id;
                }
                $hash[] = _sf('{0}-{1}', $fid, implode('-', $ids));
            }
        }


        if ($get['price_min'] || $get['price_max']) { //price
            $hash[] = _sf('p-{0}-{1}', $get['price_min'] > 0 ? $get['price_min'] : 0, $get['price_max'] > 0 ? $get['price_max'] : 0);
        }

        if (isset($get['vendors']) && count($get['vendors'])) { //vendors
            $ids = array();
            foreach ($get['vendors'] as $id) {
                $ids[] = $id;
            }
            sort($ids, SORT_NUMERIC);
            $hash[] = 'v-' . implode('-', $ids);
        }

        return implode('_', $hash);
    }

    /**
     * @param $request
     * @return array
     */
    public static function parser_request($request)
    {
        if (isset($request['feature_hash'])) {
            $filters = Filter::parser_hash($request['feature_hash']);
        } else {
            $filters = Filter::parser_hash(Filter::parser_get($request));
        }
        return $filters;
    }
}