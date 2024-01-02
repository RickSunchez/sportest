<?php
namespace CMS\Mail\Entity;

use Delorius\Core\ORM;

class SubscriberGroup extends ORM {

    protected $_primary_key = "id";
    protected $_table_name = "df_group_sub";


    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'sub_id' => array(
            'column_name' => 'sub_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'group_id' => array(
            'column_name' => 'group_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
    );

}