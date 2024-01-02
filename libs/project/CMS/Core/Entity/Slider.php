<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;

/**
 * @property int $id Primary key
 * @property int $pos Priority
 * @property int $status Active
 * @property string $code code
 * @property string $title Title
 * @property string $url URL by go
 */
class Slider extends ORM
{
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

    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    protected function behaviors()
    {
        return array(
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'sliders',
                'ratio_fill' => true,
                'background_color' => '#ffffff',
                'preview_width' => 200,
                'preview_height' => 200,
                'normal_width' => 2000,
                'normal_height' => 1200
            ),
        );
    }

    protected function rules()
    {
        return array(
            'title' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 200), 'Укажите названия слайдера'),
            ),
        );
    }

    protected $_table_name = 'df_sliders';
    protected $_table_columns_set = array('title');


    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
            'column_default' => 'top',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'title' => array(
            'column_name' => 'title',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        )
    );
}