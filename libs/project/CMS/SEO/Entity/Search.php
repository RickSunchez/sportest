<?php
namespace CMS\SEO\Entity;

use Delorius\Core\ORM;

class Search extends ORM
{

    /**
     * @return $this
     */
    public function sort()
    {
        return $this->order_by('count', 'desc')
            ->order_by('date_edit','desc')
            ->order_pk();
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['edited'] = $arr['date_edit'] ? date('d.m.Y H:i', $arr['date_edit']) : date('d.m.Y H:i', $arr['date_cr']);
        return $arr;
    }


    protected function rules()
    {
        return array(
            'type' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 10), 'Укажите тип поиска'),
            ),
            'query' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 100), 'Укажите запрос'),
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

    protected $_table_name = 'seo_search';
    protected $_table_columns_set = array('query');

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
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'varchar',
            'character_maximum_length' => 10,
            'collation_name' => 'utf8_general_ci',
        ),
        'query' => array(
            'column_name' => 'query',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'query_str' => array(
            'column_name' => 'query_str',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 32,
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

}