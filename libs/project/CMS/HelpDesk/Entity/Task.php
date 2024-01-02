<?php
namespace CMS\HelpDesk\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;

/**
 * Class Task
 * @package CMS\HelpDesk\Entity
 *
 * @property int $task_id Primary key
 * @property int $user_id Current user id
 * @property int $count_msg Count message
 * @property int $status Status task
 * @property int $type_id Type
 * @property int $read_admin Read to admin
 * @property int $read_user Read to user
 * @property string $text Text
 * @property int $date_cr Date time created
 * @property int $date_edit Date time last edit
 */
class Task extends ORM
{
    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_created('DESC');
        return $this;
    }


    const STATUS_CREATE = 1;
    const STATUS_ADOPTED = 2;
    const STATUS_CLOSE = 3;

    /**
     * @return array
     */
    public static function getStatus()
    {
        return array(
            self::STATUS_CREATE => 'Созданая',
            self::STATUS_ADOPTED => 'Принятая',
            self::STATUS_CLOSE => 'Закрытая',
        );
    }

    /**
     * @return string
     */
    public function getNameStatus()
    {
        $types = self::getStatus();
        return $types[$this->status];
    }


    const TYPE_BID = 1;
    const TYPE_ABUSE = 2;

    /**
     * @return array
     */
    public static function getType()
    {
        return array(
            self::TYPE_BID => 'Заявка',
            self::TYPE_ABUSE => 'Жалоба'

        );
    }

    /**
     * @return string
     */
    public function getNameType()
    {
        $types = self::getType();
        return $types[$this->type_id];
    }


    protected $_primary_key = 'task_id';
    protected $_table_name = 'df_task';
    protected $_table_columns_set = array('subject', 'text');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE, // 'd.m.Y H:i'
    );


    public function rules()
    {
        return array(
            'subject' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 400), 'Укажите название задачи'),
            ),
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 20000), 'Укажите ваш комментарий к задачи'),
            )
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']], true);
        $arr['updated'] = $arr[$this->_updated_column['column']] ? DateTime::dateFormat($arr[$this->_updated_column['column']], true) : '';
        $arr['status_name'] = $this->getNameStatus();
        $arr['type_name'] = $this->getNameType();
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'userBehavior' => 'CMS\Users\Behaviors\UserBehavior',
            'editTaskBehavior' => 'CMS\HelpDesk\Behaviors\EditTaskBehavior',
        );
    }

    protected $_table_columns = array(
        'task_id' => array(
            'column_name' => 'task_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'count_msg' => array(
            'column_name' => 'count_msg',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'type_id' => array(
            'column_name' => 'type_id',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'read_admin' => array(
            'column_name' => 'read_admin',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'read_user' => array(
            'column_name' => 'read_user',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'is_admin' => array(
            'column_name' => 'is_admin',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'subject' => array(
            'column_name' => 'subject',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
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
    );

}