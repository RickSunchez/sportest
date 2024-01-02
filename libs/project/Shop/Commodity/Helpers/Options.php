<?php

namespace Shop\Commodity\Helpers;

use CMS\Core\Entity\Image;
use Delorius\Core\Environment;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Options\Inventory;
use Shop\Commodity\Entity\Options\Item;
use Shop\Commodity\Entity\Options\Variant;

class Options
{
    //array('goods_id', 'url', 'name', 'value_old', 'value', 'code','amount', 'article');

    /**
     * @param $goods_list
     * @param $ids
     * @param bool|false $is_image
     * @throws \Delorius\Exception\Error
     */
    public static function acceptFirstVariantsByProducts(&$goods_list, $ids, $is_image = true)
    {
        if (count($ids) == 0) {
            return;
        }

        if ($is_image) {
            $images_main = Image::model()
                ->select('image_id', 'target_id', 'name', 'normal', 'preview')
                ->whereByTargetType(Goods::model())
                ->whereByTargetId($ids)
                ->main(true)
                ->find_all();
            $images_main = Arrays::resultAsArrayKey($images_main, 'target_id');
        }

        #variants
        $result = self::getFirstVariants($ids);
        $goods_vars = $variants = array();
        foreach ($result as $var) {
            $variants[$var['goods_id']][$var['id']] = $var;
            $goods_vars[$var['option_id']] = $var['id'];
        }

        $goods_option = $combinations = array();
        foreach ($goods_list as &$goods) {

            if (is_array($goods)) {
                $goods = Goods::mock($goods);
            }

            if ($is_image) {
                if (isset($images_main[$goods->pk()])) {
                    $goods->image = (object)$images_main[$goods->pk()];
                }
            }

            $goods->combination_hash = self::getCartId($goods->pk(), $variants[$goods->pk()]);

            if (isset($variants[$goods->pk()]) && count($variants[$goods->pk()])) {
                $orig_value = $goods->value;
                foreach ($variants[$goods->pk()] as $id => $variant) {
                    $goods->value = self::acceptVariant($orig_value, $goods->value, $variant['modifier'], $variant['type']);

                    if ($variant['inventory']) {
                        $goods_option[$variant['option_id']] = $variant['id'];
                    }
                }
            }

            if (count($goods_option)) {
                $combination = self::getCartId($goods->pk(), $goods_option);
                $combinations[$goods->pk()] = $combination;
            }
            $goods_option = array();
        }

        if (count($combinations)) {
            $inventories = Inventory::model()->active()->where('combination_hash', 'in', $combinations)->find_all();
            $inventories = Arrays::resultAsArrayKey($inventories, 'goods_id');

            if ($is_image) {
                $images_inventory = Image::model()
                    ->select('image_id', 'target_id', 'name', 'normal', 'preview')
                    ->whereByTargetType(Inventory::model())
                    ->whereByTargetId($combinations)
                    ->find_all();
                $images_inventory = Arrays::resultAsArrayKey($images_inventory, 'target_id');
            }

            foreach ($goods_list as &$goods) {

                if (isset($inventories[$goods->pk()])) {
                    $inventory = $inventories[$goods->pk()];
                    $inventory->accept($goods, false);

                    if ($is_image) {

                        if (isset($images_inventory[$inventory->pk()])) {
                            $goods->image = (object)$images_inventory[$inventory->pk()];
                        }

                    }

                }

            }

        }
    }

    /**
     * @param Goods $goods
     * @param bool|false $is_image
     */
    public static function acceptFirstVariants(Goods &$goods, $is_image = true)
    {
        $options = array();
        self::accept($goods, $options, $is_image);
    }

    /**
     * @param Goods $goods
     * @param array $options [option=>value..]
     * @param bool|true $is_image
     * @throws \Delorius\Exception\Error
     */
    public static function accept(Goods &$goods, &$options = array(), $is_image = true)
    {
        #options
        $items = Item::model()
            ->byGoodsId($goods->pk())
            ->active()
            ->sort()
            ->find_all();

        if (count($items) == 0) {
            if ($is_image && !$goods->image) {
                $image_main = $goods->getMainImage();
                if ($image_main->loaded()) {
                    $goods->image = $image_main;
                }
            }
            $goods->combination_hash = self::getCartId($goods->pk());
            return;
        }

        $ids = array();
        foreach ($items as $option) {
            $arrOptions[] = $option;
            if ($option->isInventory() && $option->isRequired()) {
                if (!isset($options[$option->pk()])) {
                    if ($option->type == Item::TYPE_FLAG) {
                        $variant = Variant::model()
                            ->select('id')
                            ->active()
                            ->where('option_id', '=', $option->pk())
                            ->byGoodsId($goods->pk())
                            ->where('pos', '=', 1)
                            ->find();

                    } else {
                        $variant = Variant::model()
                            ->active()
                            ->select('id')
                            ->where('option_id', '=', $option->pk())
                            ->byGoodsId($goods->pk())
                            ->sort()
                            ->find();
                    }
                    $ids[] = $variant['id'];
                    $options[$option->pk()] = $variant['id'];
                } else {
                    $ids[] = $options[$option->pk()];
                }
            } elseif ($option->isInventory() && !$option->isRequired()) {
                if (isset($options[$option->pk()])) {
                    $ids[] = $options[$option->pk()];
                }
            } else {
                $ids[] = $options[$option->pk()];
            }
        }

        if(!count($ids)){
            if ($is_image && !$goods->image) {
                $image_main = $goods->getMainImage();
                if ($image_main->loaded()) {
                    $goods->image = $image_main;
                }
            }
            return;
        }

        #variants
        $goods_option = $goods_option_inventory = $arrVariants = array();
        $variants = Variant::model()
            ->select('modifier', 'type', 'inventory', 'id', 'option_id', 'name')
            ->byGoodsId($goods->pk())
            ->active()
            ->where('id', 'in', $ids)
            ->find_all();

        $orig_value = $goods->value;
        foreach ($variants as $var) {
            $arrVariants[$var['option_id']] = $var;
            if ($var['inventory']) {
                $goods->value = self::acceptVariant($orig_value, $goods->value, $var['modifier'], $var['type']);
                $goods_option_inventory[$var['option_id']] = $var['id'];
            }
            $goods_option[$var['option_id']] = $var['id'];
        }

        foreach ($arrOptions as $opt) {
            $variant = self::parserVariant($arrVariants[$opt->pk()]);
            $option = self::parserOption($opt->as_array());
            $goods->options[] = array(
                'option' => $option['name'],
                'variant' => $variant['name']
            );
        }

        $goods->combination_hash = self::getCartId($goods->pk(), $goods_option);
        $combination_inventory = self::getCartId($goods->pk(), $goods_option_inventory);
        $inventory = Inventory::model()
            ->byGoodsId($goods->pk())
            ->hash($combination_inventory)
            ->find();
        if ($inventory->loaded()) {
            $inventory->accept($goods, $is_image);
        }

        if ($is_image && !$goods->image) {
            $image_main = $goods->getMainImage();
            if ($image_main->loaded()) {
                $goods->image = $image_main;
            }
        }


    }


    /**
     * @param Goods $goods
     * @param array $options
     * @return array|bool
     * @throws \Delorius\Exception\Error
     */
    public static function checkout(Goods $goods, $options = array())
    {
        #options
        $items = Item::model()
            ->byGoodsId($goods->pk())
            ->select()
            ->active()
            ->where('type', 'in', array(
                Item::TYPE_TEXT,
                Item::TYPE_VARCHAR,
                Item::TYPE_FLAG
            ))
            ->required()
            ->find_all();

        if (count($items) == 0) {
            return false;
        }

        $errors = array();
        foreach ($items as $option) {
            if (!isset($options[$option['id']])) {
                $errors[] = $option;
            }
        }
        return count($errors) ? $errors : false;
    }

    /**
     * Генераци всех возможных вариантов
     * @param $goodsId
     * @param bool|true $update обновить|удалить существующий вариант
     * @throws \Delorius\Exception\Error
     */
    public static function genCombinationsByGoods($goodsId, $update = true)
    {
        $items = Item::model()
            ->byGoodsId($goodsId)
            ->active()
            ->where('type', 'in', array(
                Item::TYPE_RADIO,
                Item::TYPE_SELECT,
                Item::TYPE_FLAG
            ))
            ->inventory()
            ->find_all();

        $indexIds = $options = array();
        foreach ($items as $key => $item) {
            $indexIds[$item->pk()] = $key;
            $options[] = $item->pk();
        }

        $result = Variant::model()
            ->byGoodsId($goodsId)
            ->active()
            ->find_all();

        $variants = array();
        foreach ($result as $var) {
            $index = $indexIds[$var->option_id];
            $variants[$index][] = $var->pk();
        }

        $combinations = self::getAllCombinations($options, $variants);

        if ($update) {
            DB::update(Inventory::model()->table_name())
                ->set(array('status' => 0))
                ->where('goods_id', '=', $goodsId)
                ->execute(Inventory::model()->db_config());
        } else {
            $inventories = Inventory::model()->where('goods_id', '=', $goodsId)->find_all();
            foreach ($inventories as $inventory) {
                $inventory->delete();
            }

        }

        $inventories = Inventory::model()->byGoodsId($goodsId)->find_all();
        $inventories = Arrays::resultAsArrayKey($inventories, 'combination_hash');

        $count = count($combinations);
        foreach ($combinations as $key => $combination) {
            $combination_hash = self::getCartId($goodsId, $combination);
            $combination_str = self::arrayToStrCombination($combination);
            if (isset($inventories[$combination_hash])) {
                $inventories[$combination_hash]->status = 1;
                $inventories[$combination_hash]->combination = $combination_str;
                $inventories[$combination_hash]->pos = $key;
                $inventories[$combination_hash]->save();
            } else {

                try {
                    $inv = new Inventory();
                    $inv->values(array(
                        'combination' => $combination_str,
                        'goods_id' => $goodsId,
                        'status' => 1,
                        'pos' => ($count - $key)
                    ));
                    $inv->combination_hash = $combination_hash;
                    $inv->save();
                } catch (OrmValidationError $e) {
                    Environment::getContext()->getService('logger')
                        ->error(
                            $e->getErrorsMessage(),
                            _sf('genCombinationsByGoods:{0}:{1}', $goodsId, $combination_str)
                        );
                }
            }
        }

        // delete inventory by status = 0
        $inventories = Inventory::model()
            ->where('status', '=', 0)
            ->where('goods_id', '=', $goodsId)
            ->find_all();
        foreach ($inventories as $inventory) {
            $inventory->delete();
        }
    }

    /**
     * Gets all possible options combinations
     *
     * @param array $options Options identifiers
     * @param array $variants Options variants identifiers in the order according to the $options parameter
     * @return array Combinations
     */
    public static function getAllCombinations($options, $variants)
    {
        $combinations = array();

        // Take first option
        $options_key = array_keys($options);
        $variant_number = reset($options_key);
        $option_id = $options[$variant_number];

        // Remove current option
        unset($options[$variant_number]);

        // Get combinations for other options
        $sub_combinations = !empty($options) ? self::getAllCombinations($options, $variants) : array();

        if (!empty($variants[$variant_number])) {
            // run through variants
            foreach ($variants[$variant_number] as $variant) {
                if (!empty($sub_combinations)) {
                    // add current variant to each subcombination
                    foreach ($sub_combinations as $sub_combination) {
                        $sub_combination[$option_id] = $variant;
                        $combinations[] = $sub_combination;
                    }
                } else {
                    $combinations[] = array(
                        $option_id => $variant
                    );
                }
            }
        } else {
            $combinations = $sub_combinations;
        }

        return $combinations;
    }

    /**
     * Constructs a string in format [option1=>variant1,option2=>variant2, ...]
     * @param array $goods_options
     * @return string
     */
    public static function arrayToStrCombination($goods_options)
    {
        if (empty($goods_options) && !is_array($goods_options)) {
            return '';
        }

        $combination = '';
        foreach ($goods_options as $option => $variant) {
            $combination .= $option . '_' . $variant . '_';
        }
        $combination = trim($combination, '_');

        return $combination;
    }

    /**
     * @param $combination_str
     * @return array
     */
    public static function strToArrayCombination($combination_str)
    {
        $combination = explode('_', $combination_str);
        $options = $varIds = $optIds = array();
        foreach ($combination as $key => $value) {
            if ($key % 2) {
                $varIds[] = $value;
            } else {
                $optIds[] = $value;
            }
        }
        foreach ($optIds as $key => $value) {
            $options[$value] = $varIds[$key];
        }

        return $options;
    }

    /**
     * Calculate unique product id in the cart
     * @param int $goodsId
     * @param array $goods_options [option1=>variant1,option2=>variant2, ...]
     * @return string
     */
    public static function getCartId($goodsId, $goods_options = array())
    {
        $_cid = array();
        if (!empty($goods_options) && is_array($goods_options)) {

            foreach ($goods_options as $k => $v) {
                $_cid[] = $v;
            }
        }
        natsort($_cid);
        array_unshift($_cid, $goodsId);
        $cart_id = Strings::crc32(implode('_', $_cid));
        return $cart_id;
    }

    /**
     * @param int|array $goodsIds
     * @return object Variants
     */
    public static function getFirstVariants($goodsIds)
    {
        $table = Variant::model();
        return DB::select()
            ->from(
                DB::expr(
                    '(' .
                    DB::select('v1.option_id', DB::expr('MAX(`v1`.`pos`) as max_pos'))
                        ->group_by('v1.option_id')
                        ->where('v1.status', '=', 1)
                        ->where('v1.required', '=', 1)
                        ->where('v1.goods_id', is_array($goodsIds) ? 'IN' : '=', $goodsIds)
                        ->order_by('v1.id')
                        ->from(array($table->table_name(), 'v1'))
                    . ') as temp'
                )
            )
            ->join(array($table->table_name(), 'v2'))
            ->on('temp.max_pos', '=', 'v2.pos')
            ->on('temp.option_id', '=', 'v2.option_id')
            ->group_by('v2.option_id')
            ->execute($table->db_config());
    }

    /**
     * @param null $typeId
     * @return array
     */
    public static function getTypes($typeId = null)
    {
        $types = Item::getTypes();
        if ($typeId == null) {
            return $types;
        } elseif ($typeId == Item::TYPE_TEXT || $typeId == Item::TYPE_VARCHAR) {
            unset(
                $types[Item::TYPE_SELECT],
                $types[Item::TYPE_RADIO],
                $types[Item::TYPE_FLAG]
            );
        } elseif ($typeId == Item::TYPE_FLAG) {
            unset(
                $types[Item::TYPE_TEXT],
                $types[Item::TYPE_VARCHAR],
                $types[Item::TYPE_SELECT],
                $types[Item::TYPE_RADIO]);
        } else {
            unset(
                $types[Item::TYPE_TEXT],
                $types[Item::TYPE_VARCHAR],
                $types[Item::TYPE_FLAG]
            );
        }
        return $types;
    }

    /**
     * @param float $value
     * @param float $modifier
     * @param int $type
     * @return float|int
     */
    protected static function acceptVariant($orig_value, $value, $modifier, $type)
    {
        if ($type == Variant::TYPE_SUM) {
            if ($modifier{0} == '-') {
                $value = $value - floatval(substr($modifier, 1));
            } else {
                $value = $value + floatval($modifier);
            }
        }

        if ($type == Variant::TYPE_PER) {
            if ($modifier{0} == '-') {
                $value = $value - ((floatval(substr($modifier, 1)) * $orig_value) / 100);
            } else {
                $value = $value + ((floatval($modifier) * $orig_value) / 100);
            }
        }
        $value = ($value > 0) ? $value : 0;
        return $value;
    }

    /**
     * @param array $option
     * @return array
     */
    protected static function parserOption($option)
    {
        unset($option['type_name'], $option['prefix'], $option['required'],
            $option['inventory'], $option['pos'], $option['goods_id'],
            $option['text'], $option['status']);

        return $option;
    }

    /**
     * @param array $variant
     * @return array
     */
    protected static function parserVariant($variant)
    {
        unset($variant['modifier'], $variant['inventory'], $variant['type'], $variant['option_id']);
        return $variant;
    }


}

