<?php

namespace Shop\Commodity\Entity;

use CMS\Core\Entity\Image;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Shop\Catalog\Entity\Category;

/**
 * Class Goods
 * @package Shop\Commodity\Entity
 *
 * @property int $goods_id Primary key
 * @property int $external_id  External primary key
 * @property int $external_change External change (1- yes,0- no)
 * @property int $cid Category primary key
 * @property int $сtype Category type
 * @property int $unit_id Unit primary key
 * @property string $code Code ISO money
 * @property int $vendor_id Vendor primary key (brand)
 * @property string $name Name (max 400)
 * @property string $model Model (max 400)
 * @property string $url Url (max 400)
 * @property string $article Article (max 40)
 * @property string $t_article Real Article (max 45)
 * @property string $a_articles Additional articles (max 2048)
 * @property string $brief Short description (max 500)
 * @property float $value Value of the price (12.2)
 * @property float $value_old Old value of the price (12.2)
 * @property float $value_system
 * @property int $value_of Price of ~ value (цена от ...)
 * @property int $amount The number of products in stock
 * @property int $status Status (1 - show, 0 - hide)
 * @property int $pos Position
 * @property int $minimum Minimum order
 * @property int $maximum Maximum order
 * @property int $step Step by count order
 */
class Goods extends ORM
{

    /**
     * Текущий вариант картинки если есть
     * @var Image|array
     */
    public $image;

    /**
     * @var int|string
     */
    public $combination_hash;

    /**
     * Текущие опции
     * @var array
     */
    public $options = array();


    /**
     * @param bool|true $status
     * @return $this
     */
    public function active($status = true)
    {
        $this->where($this->table_name() . '.status', '=', $status ? 1 : 0);
        return $this;
    }

    /**
     * @param bool|true $status
     * @return $this
     */
    public function moder($status = true)
    {
        $this->where($this->table_name() . '.moder', '=', $status ? 1 : 0);
        return $this;
    }

    /**
     * Тип категории
     * @param int $ctype
     * @return $this
     */
    public function ctype($ctype)
    {
        $this->where($this->table_name() . '.ctype', '=', $ctype);
        return $this;
    }

    /**
     * @param bool $status
     * @return $this
     */
    public function is_amount($status = true)
    {
        $this->where($this->table_name() . '.is_amount', '=', $status ? 1 : 0);
        return $this;
    }

    /**
     * @return mixed|string
     */
    public function getShortName()
    {
        return $this->short_name ? $this->short_name : $this->name;
    }

    /**
     * @return bool
     */
    public function isMulti()
    {
        return $this->hasBehaviors('multiCategoryBehavior');
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['price'] = $this->getPrice();
        $arr['price_raw'] = $this->getPrice(false, false);
        $arr['price_old'] = $this->getPriceOld();
        $arr['minimum'] = $this->getMinimum();
        $arr['link'] = $this->link();
        $arr['unit'] = $this->getUnit();
        $arr['image'] = $this->image ? $this->image->as_array() : null;
        $arr['combination_hash'] = $this->combination_hash;
        $arr['options'] = $this->options;
        if (!$this->isMulti()) {
            $arr['categories_str'] = $this->getCategoriesStr();
        }
        $arr['vendor'] = $this->getVendor();
        return $arr;
    }

    protected function checkCategory($value)
    {
        if (!$value)
            return true;

        $category = new Category($value);
        if (!$category->loaded())
            return false;
        return true;
    }

    protected function translate($value = null)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $str = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $str;
    }


    protected function behaviors()
    {
        return array(
            'helperPriceBehavior' => 'Shop\Store\Behaviors\HelperPriceBehavior',
            'accompanyBehavior' => 'Shop\Commodity\Behaviors\AccompanyBehavior',
            'goodsBehavior' => 'Shop\Commodity\Behaviors\GoodsBehavior',
            'editGoodsForCatalogBehavior' => 'Shop\Catalog\Behaviors\EditGoodsForCatalogBehavior',
            'sectionBehavior' => 'Shop\Commodity\Behaviors\SectionBehavior',
            'goodsCharacteristicsBehavior' => 'Shop\Commodity\Behaviors\GoodsCharacteristicsBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => false
            ),
            'attributeBehavior' => 'Shop\Commodity\Behaviors\AttributeBehavior',
            'typeBehavior' => 'Shop\Commodity\Behaviors\TypeBehavior',
            'galleryBehavior' => array(
                'class' => 'CMS\Core\Behaviors\GalleryBehavior',
                'path' => 'goods',
                'ratio_fill' => true,
                'preview_width' => 250,
                'preview_height' => 250,
            ),
        );
    }

    protected $_primary_key = 'goods_id';
    protected $_table_name = 'shop_goods';

    protected $_table_columns_set = array('url', 'cid', 'name', 'amount');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'weight' => array(
                array(array($this, 'float'))
            ),
            'minimum' => array(
                array(array($this, 'float'))
            ),
            'maximum' => array(
                array(array($this, 'float'))
            ),
            'step' => array(
                array(array($this, 'float'))
            ),
            'value' => array(
                array(array($this, 'float'))
            ),
            'value_old' => array(
                array(array($this, 'float'))
            ),
            'amount' => array(
                array(array($this, 'float'))
            ),
        );
    }

    protected function float($value)
    {
        return str_replace(',', '.', $value);
    }

    protected function setAmount($value)
    {
        if ($value > 0) {
            $this->is_amount = 1;
        } else {
            $this->is_amount = 0;
        }
        return str_replace(',', '.', $value);
    }

    protected function rules()
    {
        return array(
            'cid' => array(
                array(array($this, 'checkCategory'), array(':value'), 'Данной категории не существует'),
            ),
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 400), 'Укажите название товара'),
            ),
        );
    }

    protected $_table_columns = array(
        'goods_id' => array(
            'column_name' => 'goods_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'external_id' => array(
            'column_name' => 'external_id',
            'data_type' => 'varchar',
            'character_maximum_length' => 36,
            'collation_name' => 'utf8_general_ci',
        ),
        'external_change' => array(
            'column_name' => 'external_change',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'cid' => array(
            'column_name' => 'cid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'ctype' => array(
            'column_name' => 'ctype',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => Category::TYPE_GOODS
        ),
        'unit_id' => array(
            'column_name' => 'unit_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 1
        ),
        'vendor_id' => array(
            'column_name' => 'vendor_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'provider_id' => array(
            'column_name' => 'provider_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'short_name' => array(
            'column_name' => 'short_name',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'model' => array(
            'column_name' => 'model',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'article' => array(
            'column_name' => 'article',
            'data_type' => 'varchar',
            'character_maximum_length' => 40,
            'collation_name' => 'utf8_general_ci',
        ),
        't_article' => array(
            'column_name' => 't_article',
            'data_type' => 'varchar',
            'character_maximum_length' => 45,
            'collation_name' => 'utf8_general_ci',
        ),
        'a_articles' => array(
            'column_name' => 'a_articles',
            'data_type' => 'varchar',
            'character_maximum_length' => 2048,
            'collation_name' => 'utf8_general_ci',
        ),
        'brief' => array(
            'column_name' => 'brief',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 10,
            'collation_name' => 'utf8_general_ci',
            'column_default' => SYSTEM_CURRENCY
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'value_system' => array(
            'column_name' => 'value_system',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'value_old' => array(
            'column_name' => 'value_old',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'value_of' => array(
            'column_name' => 'value_of',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'amount' => array(
            'column_name' => 'amount',
            'data_type' => 'decimal',
            'exact' => 1,
            'numeric_precision' => 10,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'is_amount' => array(
            'column_name' => 'is_amount',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'rating' => array(
            'column_name' => 'rating',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 6,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
        'votes' => array(
            'column_name' => 'votes',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0
        ),
        'minimum' => array(
            'column_name' => 'minimum',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 3,
            'column_default' => 1
        ),
        'maximum' => array(
            'column_name' => 'maximum',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 3,
            'column_default' => 0
        ),
        'step' => array(
            'column_name' => 'step',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 3,
            'column_default' => 1
        ),
        'weight' => array(
            'column_name' => 'weight',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 3,
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
        'prefix' => array(
            'column_name' => 'prefix',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'update' => array(
            'column_name' => 'update',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'moder' => array(
            'column_name' => 'moder',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'inner' => array(
            'column_name' => 'inner',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'popular' => array(
            'column_name' => 'popular',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'video_id' => array(
            'column_name' => 'video_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
    );
}