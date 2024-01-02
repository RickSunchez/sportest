<?php
namespace CMS\Mail\Entity;

use CMS\Mail\Model\Notification\NotifySubscriber;
use Delorius\Core\ORM;

class Subscriber extends ORM {

    protected $_table_name = 'df_subscribers';

    protected $_table_columns_set = array('email');

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );


    public function rules(){
        return array(
            'email'=>array(
                array('\\Delorius\\Utils\\Validators::isEmail',array(':value'),'Неверно указан email'),
            )
        );
    }

    /**
     * @param $groupId
     * @return $this
     */
    public function whereSubscriptionId($groupId){
        $table_group = SubscriberGroup::model()->table_name();
        $this->join($table_group,'INNER')
            ->on($this->table_name().'.id','=', $table_group.'.sub_id')
            ->where( $table_group.'.group_id','=',$groupId)
            ->select($this->table_name().'.*');
        return $this;
    }

    /**
     * @param string $subject
     * @param string $message
     * @param int $groupId
     * @return bool
     */
    public function sendMessage($subject,$message,$groupId = 0){
        if(!$this->loaded())
            return false;
        $notify = new NotifySubscriber($this);
        $notify->setGroupId($groupId);
        return $notify->send($subject,$message);
    }


    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name'=> 'utf8_general_ci'
        ),
        'email' => array(
            'column_name' => 'email',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name'=> 'utf8_general_ci'
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default' => 1
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 45,
            'collation_name'=> 'utf8_general_ci'
        ),
        'ip' => array(
            'column_name' => 'ip',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name'=> 'utf8_general_ci'
        ),
    );



}