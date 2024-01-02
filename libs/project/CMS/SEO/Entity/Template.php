<?php
namespace CMS\SEO\Entity;

use CMS\SEO\Model\Helpers;
use Delorius\Core\ORM;

class Template extends ORM
{


    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = $arr['date_cr'] ? date('d.m.Y H:i', $arr['date_cr']) : null;
        return $arr;
    }


    protected function rules()
    {
        return array(
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50000), 'Введите текст шаблона'),
            ),
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Введите название шаблона'),
            ),
        );
    }

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_table_name = 'seo_template';
    protected $_table_columns_set = array('text', 'name');

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'count' => array(
            'column_name' => 'count',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 1
        ),
        'step' => array(
            'column_name' => 'step',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 1
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
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
        ),

    );

    protected function init()
    {
        $this->onBeforeDelete[] = callback(function ($orm) {
            Helpers::delete($orm->pk());
        });
    }

}