<?php
namespace CMS\Users\Entity;

use Delorius\Core\ORM;

class Role extends ORM
{
    protected $_primary_key = 'role_id';
    protected $_table_name = 'df_roles';

    protected $_table_columns = array(
        'role_id' => array(
            'column_name' => 'role_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'pid' => array(
            'column_name' => 'pid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'varchar',
            'character_maximum_length' => 40,
            'collation_name' => 'utf8_general_ci',
        ),
        'is_root' => array(
            'column_name' => 'is_root',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default' => 0
        ),
    );
}