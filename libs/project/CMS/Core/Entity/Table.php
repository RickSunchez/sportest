<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;

class Table extends ORM
{

    /** @return \CMS\Core\Entity\Comment */
    public function whereByTargetType(ORM $orm)
    {
        $this->where('target_type', '=', $orm->table_name());
        return $this;
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_pk();
        return $this;
    }

    protected function rules()
    {
        return array(
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 2000), 'Введите текст комментария'),
            ),
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        return $arr;
    }

    protected $_primary_key = 'id';
    protected $_table_name = 'df_table';

    protected $_table_columns_set = array('target_type');


    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'target_type' => array(
            'column_name' => 'target_type',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
    );
}