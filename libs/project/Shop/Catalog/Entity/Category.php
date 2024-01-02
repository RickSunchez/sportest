<?php

namespace Shop\Catalog\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

/**
 * Class Category
 * @package Shop\Catalog\Entity
 *
 * @property int $cid Primary key
 * @property int $external_id  External primary key
 * @property int $external_change External change (1- yes,0- no)
 * @property int $pid Parent primary key
 * @property int $type_id Type
 * @property string $name Name (max 200)
 * @property string $header Headline category (max 200)
 * @property string $url Url (max 200)
 * @property string $text_top Text top
 * @property string $text_below Text below
 * @property int $children Count of children with parent
 * @property int $goods Count of goods
 * @property int $pos Position
 * @property int $status Status (1 - show, 0 - hide)
 */
class Category extends ORM
{

    const TYPE_GOODS = 1;

    /** @return array Types */
    public static function getTypes()
    {
        return array(
            self::TYPE_GOODS => 'Продукция',
        );
    }

    /**
     * @return string
     */
    public function getNameType()
    {
        $types = self::getTypes();
        return $types[$this->type_id];
    }

    /**
     * @param $typeId
     * @return $this
     */
    public function type($typeId)
    {
        $this->where($this->table_name() . '.type_id', '=', $typeId);
        return $this;
    }

    /**
     * @return string
     */
    public function getHeaderTitle()
    {
        return $this->header ? $this->header : $this->name;
    }

    /**
     * @param null $id
     * @return Category
     */
    public function parent($id = null)
    {
        if ($id == null)
            $this->where($this->table_name() . '.pid', '=', 0);
        else
            $this->where($this->table_name() . '.pid', '=', $id);
        return $this;
    }

    /**
     * @return Category
     */
    public function active()
    {
        $this->where($this->table_name() . '.status', '=', 1);
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
            'categoryBehavior' => 'Shop\Catalog\Behaviors\CategoryBehavior',
            'metaGoodsBehavior' => 'Shop\Catalog\Behaviors\MetaGoodsBehavior',
            'helperCategoryBehavior' => 'Shop\Catalog\Behaviors\HelperCategoryBehavior',
            'editCategoryBehavior' => 'Shop\Catalog\Behaviors\EditCategoryBehavior',
            'categoryPopularProductBehavior' => 'Shop\Catalog\Behaviors\CategoryPopularProductBehavior',
            'filterBehavior' => 'Shop\Catalog\Behaviors\FilterBehavior',
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => false,
                'desc' => false
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'category',
                'ratio_fill' => true,
                'preview_width' => 250,
                'preview_height' => 250
            )
        );
    }

    protected $_primary_key = 'cid';
    protected $_table_name = 'shop_category';
    protected $_table_columns_set = array('url', 'pid', 'type_id');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'type_id' => array(
                array(array($this, 'setTypeId'))
            ),
        );
    }

    protected function rules()
    {
        return array(
            'pid' => array(
                array(array($this, 'checkParent'), array(':value'), 'К данной категории нельзя добавить дочерний каталог'),
            ),
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Укажите название категории'),
            ),
        );
    }

    protected function setTypeId($value)
    {
        $types = self::getTypes();
        if (isset($types[$value])) {
            return $value;
        } else {
            return self::TYPE_GOODS;
        }

    }

    protected function checkParent($value)
    {
        if (!$value)
            return true;

        $category = new self($value);
        if (!$category->loaded())
            return false;

        return true;
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
        'cid' => array(
            'column_name' => 'cid',
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
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default' => 1
        ),
        'pid' => array(
            'column_name' => 'pid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => self::TYPE_GOODS
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
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
        'children' => array(
            'column_name' => 'children',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'goods' => array(
            'column_name' => 'goods',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
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
        'prefix_goods' => array(
            'column_name' => 'prefix_goods',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'show_cats' => array(
            'column_name' => 'show_cats',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
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
        'popular' => array(
            'column_name' => 'popular',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
    );
}