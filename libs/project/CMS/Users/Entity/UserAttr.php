<?php
namespace CMS\Users\Entity;

use Delorius\Core\ORM;

class UserAttr extends ORM{

    public function rules(){
        return array(
            'value' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 400), _t('CMS:Users','The name must be from {0} to {1} characters',1,400))
            ),
        );
    }

    protected $_table_name = 'df_users_attr';
    protected $_primary_key = 'id';

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'int unsigned',
            'display' => 11
        ),
        'attr_id' => array(
            'column_name' => 'attr_id',
            'data_type' => 'int unsigned',
            'display' => 11
        ),
        'group_id' => array(
            'column_name' => 'group_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        )
    );
}