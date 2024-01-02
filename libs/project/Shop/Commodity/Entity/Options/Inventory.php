<?php
namespace Shop\Commodity\Entity\Options;

use Delorius\Core\ORM;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Helpers\Options;

class Inventory extends ORM
{

    /**
     * @param Goods $goods
     * @param bool|false $is_image
     */
    public function accept(Goods &$goods, $is_image = false)
    {
        if ($goods->pk() == $this->goods_id) {

            if ($this->goods_article)
                $goods->article = $this->goods_article;

            if ($goods->amount == 0 && $this->goods_amount > 0)
                $goods->amount = $this->goods_amount;

            if($is_image){
                $image = $this->getImage();
                if ($image->loaded()) {
                    $goods->image = $image;
                }
            }

        }
    }

    /**
     * @param int $combination_hash
     * @return $this
     */
    public function hash($combination_hash)
    {
        $this->where('combination_hash', '=', $combination_hash);
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }

    /**
     * @param int|array $id
     * @return $this
     */
    public function byGoodsId($id)
    {
        if (is_array($id)) {
            $this->where('goods_id', 'IN', $id);
        } else {
            $this->where('goods_id', '=', $id);
        }
        return $this;
    }

    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by($this->table_name() . '.pos', $direction)->order_pk();
        return $this;
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['options'] = Options::strToArrayCombination($arr['combination']);
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'inventory',
                'ratio_fill' => true,
                'preview_width' => 250,
                'preview_height' => 250,
            ),
        );
    }

    protected $_primary_key = 'combination_hash';
    protected $_table_name = 'shop_goods_options_inventory';

    protected $_table_columns = array(
        'combination_hash' => array(
            'column_name' => 'combination_hash',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'combination' => array(
            'column_name' => 'combination',
            'data_type' => 'varchar',
            'character_maximum_length' => 255,
            'collation_name' => 'utf8_general_ci',
        ),
        'goods_article' => array(
            'column_name' => 'goods_article',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'goods_amount' => array(
            'column_name' => 'goods_amount',
            'data_type' => 'decimal',
            'exact' => 1,
            'numeric_precision' => 10,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),

    );
}