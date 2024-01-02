<?php

namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;
use Shop\Catalog\Entity\Category;

class YmlGenerator extends ORM
{

    protected $_table_name = 'shop_yml_generator';
    protected $_config_key = 'config';
    protected $_table_columns_set = array('file');

    public function behaviors()
    {
        return array(
            'editYmlBehavior' => 'Shop\Commodity\Behaviors\EditYmlBehavior',
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['exist'] = $exist = $this->isExists();
        $arr['path'] = $this->getPath(false);
        if ($exist)
            $arr['exist_date'] = date("d.m.Y", filectime($this->getPath()));

        return $arr;
    }

    protected function rules()
    {
        return array(
            'file' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 50), 'Укажите название файла'),
                array(array($this, 'unique'), array('file', ':value'), 'Файл с таким именем существует')
            ),
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50), 'Укажите название файла'),
            ),
            'company' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 100), 'Укажите название файла'),
            ),
            'site' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50), 'Укажите название файла'),
            )
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
        'ctype' => array(
            'column_name' => 'ctype',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => Category::TYPE_GOODS
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'company' => array(
            'column_name' => 'company',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'site' => array(
            'column_name' => 'site',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'file' => array(
            'column_name' => 'file',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'utm' => array(
            'column_name' => 'utm',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'config' => array(
            'column_name' => 'config',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'adult' => array(
            'column_name' => 'adult',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'delivery' => array(
            'column_name' => 'delivery',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'pickup' => array(
            'column_name' => 'pickup',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'store' => array(
            'column_name' => 'store',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'amount' => array(
            'column_name' => 'amount',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'params' => array(
            'column_name' => 'params',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'sales_notes' => array(
            'column_name' => 'sales_notes',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),

    );
}