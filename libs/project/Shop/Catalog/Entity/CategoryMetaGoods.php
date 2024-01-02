<?php

namespace Shop\Catalog\Entity;

use CMS\Core\Helper\ParserString;
use Delorius\Core\ORM;
use Delorius\Utils\Arrays;

class CategoryMetaGoods extends ORM
{

    public function getText()
    {
        if (!$this->text) {
            return '';
        }

        $parser = new ParserString($this->_values);
        return $parser->render($this->text);
    }

    public function getTitle()
    {
        if (!$this->title) {
            return '';
        }

        $parser = new ParserString($this->_values);
        return $parser->render($this->title);
    }

    public function getDesc()
    {
        if (!$this->desc) {
            return '';
        }

        $parser = new ParserString($this->_values);
        return $parser->render($this->desc);
    }

    protected $_values = array();

    public function setKey($name, $values)
    {
        $this->_values[$name] = $values;
    }

    public function getKey($name)
    {
        return Arrays::get($this->_values, $name);
    }


    protected $_table_name = 'shop_category_meta_goods';
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
        'title' => array(
            'column_name' => 'title',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'desc' => array(
            'column_name' => 'desc',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        )
    );
}