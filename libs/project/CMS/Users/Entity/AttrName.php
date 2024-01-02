<?php
namespace CMS\Users\Entity;

use Delorius\Core\ORM;

class AttrName extends ORM{

    /**
     * @param int $group
     * @return $this
     */
    public function whereByGroup($group=0){
        $this->where('group_id', '=', $group);
        return $this;
    }

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = NULL)
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    public function behaviors(){
        return array(
            'attrUserGroupBehaviors' => 'CMS\Users\Behaviors\AttrUserGroupBehavior'
        );
    }

    protected $_table_columns_set = array('code');
    protected $_table_name = 'df_user_attr_name';
    protected $_primary_key = 'id';

    public function rules(){
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), _t('CMS:Users','The name must be from {0} to {1} characters',1,200))
            ),
            'code' => array(
                array(array($this,'unique'),array('code',':value'),_t('CMS:Users','This code already exists')),
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 20), _t('CMS:Users','The сщву must be from {0} to {1} characters',1,20))
            ),
        );
    }

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'group_id' => array(
            'column_name' => 'group_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci'
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
            'key' => 'UNI'
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'require' => array(
            'column_name' => 'require',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'active' => array(
            'column_name' => 'active',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        )
    );
}