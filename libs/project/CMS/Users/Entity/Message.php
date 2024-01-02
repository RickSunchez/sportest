<?php
namespace CMS\Users\Entity;

use Delorius\Core\ORM;

class Message extends ORM
{
    const STATUS_NEW = 0;
    const STATUS_READ = 1;
    const STATUS_DELETE = 2;

    public function whereDialog($owner_id,$to_id){
        $this->where_open()
            ->where('owner_id','=',$owner_id)
            ->where('to_id','=',$to_id)
            ->where('owner_status','in',array(self::STATUS_NEW,self::STATUS_READ))
        ->where_close()
        ->or_where_open()
            ->where('owner_id','=',$to_id)
            ->where('to_id','=',$owner_id)
            ->where('to_status','in',array(self::STATUS_NEW,self::STATUS_READ))
        ->or_where_close();
        return $this;
    }

    public function rules(){
        return array(
            'text'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',1,20000),_t('CMS:Users','Enter the text message')),
            )
        );
    }

    protected $_primary_key = 'msg_id';
    protected $_table_name = 'df_messages';
    protected $_table_columns_set = array('text');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'msg_id' => array(
            'column_name' => 'msg_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'owner_id' => array(
            'column_name' => 'owner_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'to_id' => array(
            'column_name' => 'to_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'owner_status' => array(
            'column_name' => 'owner_status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'to_status' => array(
            'column_name' => 'to_status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );
}