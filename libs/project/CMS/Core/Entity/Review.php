<?php

namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;

class Review extends ORM
{

    /**
     * @return string
     */
    public function link()
    {
        return link_to('review_show', array('id' => $this->pk()));
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_by('date_cr', 'DESC')->order_pk();
        return $this;
    }


    /**
     * @return $this
     */
    public function active()
    {
        $this->where('status', '=', 1);
        return $this;
    }


    public function as_array()
    {
        $arr = parent::as_array();
        $arr['created'] = DateTime::dateFormat($arr['date_cr'], true);
        $arr['date_cr'] = $arr['date_cr'] ? date('d.m.Y H:i', $arr['date_cr']) : null;
        $arr['answered'] = $arr['date_answer'] ? DateTime::dateFormat($arr['date_answer'], true) : '';
        $arr['date_answer'] = $arr['date_answer'] ? date('d.m.Y H:i', $arr['date_answer']) : null;
        return $arr;
    }


    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'date_cr' => array(
                array(array($this, 'dateConvert'))
            ),
            'date_answer' => array(
                array(array($this, 'dateConvert'))
            )
        );
    }


    protected function rules()
    {
        return array(
            'author' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 200), 'Напишите как Вас зовут'),
            ),
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 50000), 'Напишите Ваш отзыв'),
            ),
        );
    }

    protected function dateConvert($value)
    {
        if ($value == null) {
            return time();
        }
        if (preg_match('/\D/', $value) == 1) {
            return strtotime($value);
        }
        return $value;
    }


    protected $_primary_key = 'id';
    protected $_table_name = 'df_review';

    protected $_table_columns_set = array('text', 'author', 'date_cr');

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'author' => array(
            'column_name' => 'author',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'location' => array(
            'column_name' => 'location',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'callback' => array(
            'column_name' => 'callback',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'answer' => array(
            'column_name' => 'answer',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'rating' => array(
            'column_name' => 'rating',
            'data_type' => 'int unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'date_answer' => array(
            'column_name' => 'date_answer',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );
}