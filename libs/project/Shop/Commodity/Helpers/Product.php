<?php
namespace Shop\Commodity\Helpers;

use CMS\Core\Helper\Helpers;
use Delorius\DataBase\DB;
use Shop\Commodity\Entity\Accompany;
use Shop\Commodity\Entity\Attribute;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\CollectionGoods;
use Shop\Commodity\Entity\CollectionPackage;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Options\Item;
use Shop\Commodity\Entity\Review;
use Shop\Commodity\Entity\Section;
use Shop\Commodity\Entity\TypeGoods;

class Product
{

    public static function cleanse($id)
    {
        $product = Goods::mock(array('goods_id' => $id));

        #image
        $images = $product->getImages();
        if (count($images)) {
            foreach ($images as $img) {
                $img->delete();
            }
        }
        #end image


        #meta
        $meta = $product->getMeta();
        if ($meta->loaded()) {
            $meta->delete();
        }
        #end meta


        #section
        DB::delete(Section::model()->table_name())
            ->where('target_id', '=', $product->pk())
            ->where('target_type', '=', $product->table_name())
            ->execute(Section::model()->db_config());
        #end section


        #characteristics
        DB::delete(CharacteristicsGoods::model()->table_name())
            ->where('target_id', '=', $product->pk())
            ->where('target_type', '=', Helpers::getTableId($product))
            ->execute(CharacteristicsGoods::model()->db_config());
        #end characteristics


        #accompany
        DB::delete(Accompany::model()->table_name())
            ->where('current_id', '=', $product->pk())
            ->execute(Accompany::model()->db_config());
        #end accompany


        #attributes
        DB::delete(Attribute::model()->table_name())
            ->where('target_id', '=', $product->pk())
            ->where('target_type', '=', Helpers::getTableId($product))
            ->execute(Attribute::model()->db_config());
        #end attributes


        #collection
        DB::delete(CollectionPackage::model()->table_name())
            ->where('coll_id', '=', $product->pk())
            ->execute(CollectionPackage::model()->db_config());

        DB::delete(CollectionGoods::model()->table_name())
            ->where('coll_id', '=', $product->pk())
            ->execute(CollectionGoods::model()->db_config());
        #end collection


        #review
        DB::delete(Review::model()->table_name())
            ->where('goods_id', '=', $product->pk())
            ->execute(Review::model()->db_config());
        #end review


        #type
        DB::delete(TypeGoods::model()->table_name())
            ->where('goods_id', '=', $product->pk())
            ->execute(TypeGoods::model()->db_config());
        #end type


        #inventory
        $items = Item::model()->byGoodsId($product->pk())->find_all();
        foreach ($items as $item) {
            $item->delete();
        }
        #end inventory

        #update void
        DB::update($product->table_name())
            ->where('goods_id', '=', $product->pk())
            ->value('status', 0)
            ->value('model', '')
            ->value('brief', '')
            ->value('amount', 0)
            ->value('is_amount', 0)
            ->execute($product->db_config());
        #end update void


    }

}