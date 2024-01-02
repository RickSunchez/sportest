<?php

namespace CMS\Core\Entity\Config;

use Delorius\Core\ORM;

class Menu extends ORM
{

    /**
     * @return string
     */
    public function link()
    {
        if ($this->type == self::TYPE_URL) {
            return $this->value;
        } elseif ($this->type == self::TYPE_PAGE) {
            return snippet('page', $this->value);
        } elseif ($this->type == self::TYPE_CATEGORY) {
            return snippet('shop', 'category', array('id' => $this->value));
        } elseif ($this->type == self::TYPE_ROUTER) {
            list($path, $query) = explode('?', $this->value);
            $out = array();
            if ($query) {
                parse_str($query, $out);
            }
            $link = link_to($path, $out);
            return $link;
        }
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_by('pos', 'desc')->order_pk();
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

    /**
     * @param string $code
     * @return $this
     */
    public function whereByCode($code)
    {
        $this->where('code', '=', $code);
        return $this;
    }

    const TYPE_URL = 1;
    const TYPE_PAGE = 2;
    const TYPE_ROUTER = 3;
    const TYPE_CATEGORY = 4;

    /**
     * @return array
     */
    public static function getTypes()
    {
        $arr = array(
            self::TYPE_URL => 'URL',
            self::TYPE_PAGE => 'ID PAGE',
            self::TYPE_ROUTER => 'ROUTER',
        );

        if (defined('SHOP_CONFIG')) {
            $arr[self::TYPE_CATEGORY] = 'ID CATEGORY';
        }

        return $arr;
    }

    /**
     * @return string
     */
    public function getNameType()
    {
        $types = self::getTypes();
        return $types[$this->type];
    }

    public function behaviors()
    {
        return array(
            'editMenuBehavior' => 'CMS\Core\Behaviors\EditMenuBehavior',
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'menu',
                'ratio_fill' => true,
                'background_color' => '#ffffff',
                'preview_width' => 172,
                'preview_height' => 172
            ),
        );
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50), 'Введите названия пункта меню'),
            ),
            'code' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 50), 'Введите код меню'),
            ),
            'value' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 200), 'Введите url'),
            ),
        );
    }

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['type_name'] = $this->getNameType();
        $arr['link'] = $this->link();
        return $arr;
    }

    protected $_table_name = 'df_menu';

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'pid' => array(
            'column_name' => 'pid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'style' => array(
            'column_name' => 'style',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'type' => array(
            'column_name' => 'type',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => self::TYPE_URL,
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1,
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'children' => array(
            'column_name' => 'children',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
    );
}