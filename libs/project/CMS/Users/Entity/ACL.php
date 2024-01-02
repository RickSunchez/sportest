<?php
namespace CMS\Users\Entity;

use Delorius\Core\ORM;

class ACL extends ORM
{
    const DENY = 0;
    const ALLOW = 1;

    /**
     * @return array
     */
    public static function getStatus()
    {
        return array(
            self::DENY => 'Запрещено',
            self::ALLOW => 'Разрешено',
        );
    }

    /**
     * @return string
     */
    public function getNameStatus()
    {
        $status = self::getStatus();
        return $status[$this->status];
    }

    protected $_primary_key = 'acl_id';
    protected $_table_name = 'df_acl';

    protected $_table_columns = array(
        'acl_id' => array(
            'column_name' => 'acl_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'target_id' => array(
            'column_name' => 'target_id',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'target_type' => array(
            'column_name' => 'target_type',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'resource' => array(
            'column_name' => 'resource',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'privilege' => array(
            'column_name' => 'privilege',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(  // 0 - deny ,  1 - allow
            'column_name' => 'status',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default' => 0
        ),
    );
}