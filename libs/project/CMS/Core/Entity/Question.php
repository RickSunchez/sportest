<?php
namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;

class Question extends ORM
{

    /**
     * @return $this
     */
    public function sort(){
        $this->order_created('desc');
        return $this;
    }

    /**
     * @return $this
     */
    public function active(){
        $this->where('status','=',1);
        return $this;
    }


    public function as_array(){
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']],true);
        return $arr;
    }

    protected function rules(){
        return array(
            'name'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',0,200),'Напишите как Вас зовут'),
            ),
            'phone'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',0,20),'Укажите Ваш телефон'),
            ),
            'email'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',0,200),'Укажите Ваш e-mail'),
            ),
            'contact'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',0,200),'Укажите Ваш контакт'),
            ),
            'text'=>array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value',0,50000),'Укажите Ваш отзыв'),
            ),
        );
    }


    protected $_table_name = 'df_question';
    protected $_table_columns_set = array('text','name');

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

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
            'collation_name' => 'utf8_general_ci',
        ),
        'phone' => array(
            'column_name' => 'phone',
            'data_type' => 'varchar',
            'character_maximum_length' =>20,
            'collation_name' => 'utf8_general_ci',
        ),
        'email' => array(
            'column_name' => 'email',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'contact' => array(
            'column_name' => 'contact',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'mediumtext',
            'collation_name' => 'utf8_general_ci',
        ),
        'answer' => array(
            'column_name' => 'answer',
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
            'column_default' => 0,
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
    );
}