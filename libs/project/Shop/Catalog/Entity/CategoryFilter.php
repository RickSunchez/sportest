<?php

namespace Shop\Catalog\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;


class CategoryFilter extends ORM
{

    /**
     * @return $this
     */
    public function active()
    {
        $this->where($this->table_name() . '.status', '=', 1);
        return $this;
    }


    public function behaviors()
    {
        return array(
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => false,
                'desc' => false
            )
        );
    }

    protected $_primary_key = 'id';
    protected $_table_name = 'shop_category_filter_static';
    protected $_table_columns_set = array('url', 'hash', 'header');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'hash' => array(
                array(array($this, 'setHash'))
            )
        );
    }

    protected function rules()
    {
        return array(
            'header' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Укажите название'),
            ),
            'hash' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 500), 'Укажите hash фильтров до 500 символов'),
                array(array($this,'checkHash'), array(':value'), 'Укажите hash фильтров'),
            ),
        );
    }

    protected function translate($value)
    {
        if ($value == null) {
            $value = $this->header;
        }
        $str = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $str;
    }

    protected function checkHash($value)
    {
        $get = \Shop\Catalog\Helpers\Filter::parser_hash($value);
        if (count($get) == 0) {
            return false;
        }
        return true;
    }

    protected function setHash($value)
    {
        $get = \Shop\Catalog\Helpers\Filter::parser_hash($value);
        $value = \Shop\Catalog\Helpers\Filter::parser_get($get);
        return $value;
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
            'column_default' => 0
        ),
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
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
        )
    );
}