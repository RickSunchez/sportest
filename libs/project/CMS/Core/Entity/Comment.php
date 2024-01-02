<?php
namespace CMS\Core\Entity;

use CMS\Core\Helper\Jevix\JevixEasy;
use Delorius\Core\DateTime;
use Delorius\Core\ORM;

class Comment extends ORM
{
    const STATUS_MODER = 0;
    const STATUS_ACTIVE = 1;
    const STATUS_DELETE = 2;

    /** @return \CMS\Core\Entity\Comment */
    public function whereByTargetType(ORM $orm)
    {
        $this->where('target_type', '=', $orm->table_name());
        return $this;
    }

    /** @return \CMS\Core\Entity\Comment */
    public function whereByTargetId($targetId)
    {
        if (is_array($targetId)) {
            $this->where('target_id', 'IN', $targetId);
        } else {
            $this->where('target_id', '=', $targetId);
        }
        return $this;
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_pk();
        return $this;
    }

    protected function rules()
    {
        return array(
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 2000), 'Введите текст комментария'),
            ),
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr['date_cr'], true);
        $arr['edited'] = $arr['date_edit'] ? DateTime::dateFormat($arr['date_edit'], true) : null;
        $arr['html'] = JevixEasy::Parser($arr['text']);
        return $arr;
    }

    protected $_primary_key = 'comment_id';
    protected $_table_name = 'df_comment';

    protected $_table_columns_set = array('target_type', 'target_id');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'comment_id' => array(
            'column_name' => 'comment_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'target_id' => array(
            'column_name' => 'target_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'target_type' => array(
            'column_name' => 'target_type',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
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