<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Shop\Catalog\Entity\Category;

class Collection extends ORM
{
    /**
     * @return mixed|string
     */
    public function getShortName()
    {
        return $this->short_name ? $this->short_name : $this->name;
    }

    /**
     * @param int $ctype
     * @return $this
     */
    public function ctype($ctype)
    {
        $this->where($this->table_name() . '.ctype', '=', $ctype);
        return $this;
    }


    /**
     * @return $this
     */
    public function active()
    {
        $this->where($this->table_name() . '.status', '=', 1);
        return $this;
    }


    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by($this->table_name() . '.pos', $direction)->order_pk();
        return $this;
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['link'] = $this->link();
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'collectionBehavior' => 'Shop\Commodity\Behaviors\CollectionBehavior',
            'attributeBehavior' => 'Shop\Commodity\Behaviors\AttributeBehavior',
            'galleryBehavior' => array(
                'class' => 'CMS\Core\Behaviors\GalleryBehavior',
                'path' => 'collection',
                'crop' => true,
                'preview_width' => 300,
                'preview_height' => 300
            ),
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => false
            ),
        );
    }


    protected $_table_name = 'shop_collection';
    protected $_table_columns_set = array('name', 'url');
    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );
    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected function translate($value = null)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $str = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $str;
    }

    protected function checkInitId($value)
    {
        if ($value > 0) {
            return true;
        }
        return false;
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название'),
            ),
        );
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
        );
    }

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'vendor_id' => array(
            'column_name' => 'vendor_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
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
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'short_name' => array(
            'column_name' => 'short_name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
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
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
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
        'value_min' => array(
            'column_name' => 'value_min',
            'data_type' => 'decimal unsigned',
            'exact' => 1,
            'numeric_precision' => 12,
            'numeric_scale' => 2,
            'column_default' => 0
        ),
    );
}