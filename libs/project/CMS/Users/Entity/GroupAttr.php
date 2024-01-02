<?php
namespace CMS\Users\Entity;

use Delorius\Core\ORM;

class GroupAttr extends ORM{

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
            'attrGroupBehaviors' => 'CMS\Users\Behaviors\AttrGroupBehaviors'
        );
    }

    public function rules(){
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), _t('CMS:Users','The name must be from {0} to {1} characters',1,200))
            )
        );
    }

    protected $_table_name = 'df_user_attr_group';
    protected $_primary_key = 'group_id';

    protected $_table_columns = array(
        'group_id' => array(
            'column_name' => 'group_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci'
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}