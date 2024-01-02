<?php
namespace Shop\Catalog\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class Collection extends ORM
{

    /**
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->header ? $this->header : $this->name;
    }

    /**
     * @param null $direction
     * @return Category
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    /**
     * @param $typeId
     * @return $this
     */
    public function whereType($typeId)
    {
        $this->where('type_id', '=', $typeId);
        return $this;
    }

    /**
     * @return Category
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['header_title'] = $this->getHeaderTitle();
        return $arr;
    }

    public function behaviors()
    {
        return array(
            'goodsCharacteristicsBehavior' => 'Shop\Commodity\Behaviors\GoodsCharacteristicsBehavior',
            'filterBehavior' => 'Shop\Catalog\Behaviors\FilterBehavior',
            'helpCollectionBehavior' => 'Shop\Catalog\Behaviors\HelpCollectionBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => false,
                'desc' => false
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'category_collection',
                'ratio_fill' => true,
                'preview_width' => 250,
                'preview_height' => 250
            )
        );
    }

    protected $_primary_key = 'id';
    protected $_table_name = 'shop_category_collection';
    protected $_table_columns_set = array('url');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'price_min' => array(
                array(array($this, 'float'))
            ),
            'price_max' => array(
                array(array($this, 'float'))
            ),
        );
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Укажите название подборки'),
            ),
        );
    }

    protected function float($value)
    {
        return str_replace(',', '.', $value);
    }

    protected function translate($value)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $str = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $str;
    }

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'cid' => array(
            'column_name' => 'cid',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'tinyint unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => Category::TYPE_GOODS
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'vendors' => array(
            'column_name' => 'vendors',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'goods' => array(
            'column_name' => 'goods',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'cats' => array(
            'column_name' => 'cats',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'price_min' => array(
            'column_name' => 'price_min',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'price_max' => array(
            'column_name' => 'price_max',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'header' => array(
            'column_name' => 'header',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'text_top' => array(
            'column_name' => 'text_top',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'text_below' => array(
            'column_name' => 'text_below',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default' => 1
        ),
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'show_all' => array(
            'column_name' => 'show_all',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),

    );
}