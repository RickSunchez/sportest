<?php

namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;

class Register extends ORM
{
    public static function getTypes()
    {
        return array(
            \CMS\Core\Component\Register::TYPE_ATTENTION => 'Внимание',
            \CMS\Core\Component\Register::TYPE_INFO => 'Информация',
            \CMS\Core\Component\Register::TYPE_ERROR => 'Ошибка',
        );
    }

    public function getTypeName()
    {
        $types = self::getTypes();
        return $types[$this->type];
    }

    public static function getSpaces()
    {
        return array(
            \CMS\Core\Component\Register::SPACE_ADMIN => 'CPanel',
            \CMS\Core\Component\Register::SPACE_CABINET => 'ЛК',
            \CMS\Core\Component\Register::SPACE_CRON => 'Cron',
            \CMS\Core\Component\Register::SPACE_SITE => 'Site',
        );
    }

    /**
     * @return string
     */
    public function getSpaceName()
    {
        $spaces = self::getSpaces();
        return $spaces[$this->space];
    }

    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_pk($direction);
        return $this;
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['type_name'] = $this->getTypeName();
        $arr['space_name'] = $this->getSpaceName();
        $arr['created'] = DateTime::dateFormat($arr['date_cr'], true);
        return $arr;
    }

    protected $_primary_key = 'register_id';
    protected $_table_name = 'df_register';

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'register_id' => array(
            'column_name' => 'register_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'space' => array(
            'column_name' => 'space',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'ip' => array(
            'column_name' => 'ip',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'user_id' => array(
            'column_name' => 'user_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'user_namespace' => array(
            'column_name' => 'user_namespace',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
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
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'varchar',
            'character_maximum_length' => 1000,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );
}