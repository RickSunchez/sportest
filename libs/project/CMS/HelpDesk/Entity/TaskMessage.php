<?php
namespace CMS\HelpDesk\Entity;

use CMS\Core\Helper\Jevix\JevixEasy;
use Delorius\Core\ORM;

/**
 * Class TaskMessage
 * @package CMS\HelpDesk\Entity
 *
 * @property int $mid Primary key
 * @property int $user_id Current user id
 * @property int $is_admin Is current user admin (1 - yes, 0-no)
 * @property int $task_id Task
 * @property string $text Message
 * @property int $date_cr Date time created
 */
class TaskMessage extends ORM
{


    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_created('desc');
        return $this;
    }

    /** @return array */
    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = date('d.m.Y H:i', $arr[$this->_created_column['column']]);
        $arr['html'] =  \CMS\Core\Helper\Jevix\JevixEasy::Parser($this->text);
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'userBehavior' => 'CMS\Users\Behaviors\UserBehavior',
        );
    }

    protected $_table_name = 'df_task_msg';
    protected $_primary_key = 'mid';
    protected $_table_columns_set = array('text');

    public function rules()
    {
        return array(
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 20000), 'Укажите ваш комментарий к задачи'),
            )
        );
    }

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'mid' => array(
            'column_name' => 'mid',
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
        'is_admin' => array(
            'column_name' => 'is_admin',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'task_id' => array(
            'column_name' => 'task_id',
            'data_type' => 'int unsigned',
            'display' => 11
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
    );
}