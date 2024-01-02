<?php
namespace CMS\Mail\Entity;

use Delorius\Core\ORM;

class SubscriptionBid extends ORM {

    protected $_primary_key = "bid_id";
    protected $_table_name = "df_bid_sub";

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );


    protected $_table_columns = array(
        'bid_id' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'bid_id',
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',

        ),
        'phone' => array(
            'column_name' => 'phone',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',

        ),
        'email' => array(
            'column_name' => 'email',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',

        ),
        'comment' => array(
            'column_name' => 'comment',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',

        ),
        'go_id' => array(
            'column_name' => 'go_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default'=>0
        ),
        'is_mail' => array(
            'column_name' => 'is_mail',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default'=>0
        ),
        'group_id' => array(
            'column_name' => 'group_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default'=>0
        ),
        'note' => array(
            'column_name' => 'note',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',

        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default'=>0
        ),
    );



}