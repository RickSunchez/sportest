<?php

namespace CMS\SEO\Entity;

use Delorius\Core\ORM;

class Redirect extends ORM
{

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_by('pos', 'desc')->order_pk('desc');
        return $this;
    }


    const MOVE_PATH = 1;
    const MOVE_ROUTER = 2;
    const MOVE_CALLBACK = 3;

    /** @return array Types */
    public static function getMoves()
    {
        return array(
            self::MOVE_PATH => 'URL',
            self::MOVE_ROUTER => 'Router',
            self::MOVE_CALLBACK => 'Func',
        );
    }

    /**
     * @return string
     */
    public function getNameMove()
    {
        $types = self::getMoves();
        return $types[$this->type_move];
    }


    const PATH_URL = 1;
    const PATH_TMP = 2;

    /** @return array Types */
    public static function getPaths()
    {
        return array(
            self::PATH_URL => 'URL',
            self::PATH_TMP => 'TMP',
        );
    }

    /**
     * @return string
     */
    public function getNamePath()
    {
        $types = self::getPaths();
        return $types[$this->type_path];
    }


    protected function rules()
    {
        return array(
            'url' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 400), 'Укажите текущий путь'),
            ),
            'move' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 400), 'Укажите путь для редиректа'),
            ),
        );
    }


    protected $_table_name = 'seo_redirect';
    protected $_table_columns_set = array('url', 'move');

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'move' => array(
            'column_name' => 'move',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'type_url' => array(
            'column_name' => 'type_url',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'type_move' => array(
            'column_name' => 'type_move',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int',
            'display' => 11,
            'column_default' => 0,
        ),

    );

}