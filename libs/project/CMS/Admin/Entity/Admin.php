<?php
namespace CMS\Admin\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class Admin extends ORM
{

    protected $_primary_key = "admin_id";
    protected $_table_name = "df_admin";

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE, // 'd.m.Y H:i'
    );



    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'password' => array(
                array(array($this, 'hashPassword'))
            )
        );
    }

    public function hashPassword($value)
    {
        $salt = "(&4m09may)Mt[mom-)_&#myh3083ryr9pms[M)M*{#flkxjmxlkj";
        return Strings::codePassword($value . $salt);
    }

    public function as_array()
    {
        $arr = parent::as_array();
        unset($arr['password']);
        return $arr;
    }


    protected $_table_columns = array(
        'admin_id' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'admin_id',
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'login' => array(
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
            'column_name' => 'login',
        ),
        'role' => array(
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
            'column_name' => 'role',
            'column_default' => 'user'
        ),
        'password' => array(
            'column_name' => 'password',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'active' => array(
            'column_name' => 'active',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default' => 1
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int',
            'display' => 11
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0
        ),
    );

}