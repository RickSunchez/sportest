<?php
namespace Boat\Core\Entity;

use Delorius\Core\ORM;
use Delorius\DataBase\DB;

class NoteItem extends ORM
{

    /**
     * @return $this
     */
    public function sort()
    {
        $this
            ->order_by(DB::expr('cast(number as unsigned) ASC'))
            ->order_by(DB::expr('LPAD(LOWER(number), 10,0) ASC'))
            ->order_by('pos', 'DESC')
            ->order_pk();
        return $this;
    }

    /**
     * @return $this
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }


    protected $_table_name = 'boat_schema_note_item';
    protected $_table_columns_set = array();

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
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'nid' => array( #note id
            'column_name' => 'nid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'number' => array( #number №
            'column_name' => 'number',
            'data_type' => 'varchar',
            'character_maximum_length' => 5,
            'collation_name' => 'utf8_general_ci',
        ),
        'pid' => array( #product id
            'column_name' => 'pid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'article' => array(
            'column_name' => 'article',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
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
        )
    );
}