<?php
namespace CMS\Mail\Entity;

use Delorius\Core\ORM;

class Delivery extends ORM
{

    protected $_primary_key = "delivery_id";
    protected $_table_name = "df_delivery";

    protected $_table_columns_set = array('subject', 'message');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    public function rules()
    {
        return array(
            'subject' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Укажите заголовок письма'),
            ),
            'message' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 5000), 'Укажите текст письма'),
            )
        );
    }

    public function startOver()
    {
        $this->limit = 0;
        $this->offset = 0;
        $this->count = 0;
        $this->started = 0;
        $this->finished = 0;
        $this->date_start = 0;
        $this->finished = 0;
        return $this;
    }


    protected $_table_columns = array(
        'delivery_id' => array(
            'column_name' => 'delivery_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'subject' => array(
            'column_name' => 'subject',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'message' => array(
            'column_name' => 'message',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'group_id' => array(
            'column_name' => 'group_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'limit' => array(
            'column_name' => 'limit',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'offset' => array(
            'column_name' => 'offset',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'count' => array(
            'column_name' => 'count',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'started' => array(
            'column_name' => 'started',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'date_start' => array(
            'column_name' => 'date_start',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'finished' => array(
            'column_name' => 'finished',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'date_end' => array(
            'column_name' => 'date_end',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );

}