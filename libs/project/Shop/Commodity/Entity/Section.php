<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

/**
 * Class Section
 * @package Shop\Commodity\Entity
 *
 * @property int $section_id Primary key
 * @property int $target_id Target object
 * @property string $target_type Target object type
 * @property string $name Name section
 * @property string $url Url
 * @property string $text Text
 * @property int $status Status (1 - show,0 - hide)
 * @property int $pos Position
 */
class Section extends ORM
{

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    /**
     * @return $this
     */
    public function active(){
        $this->where('status','=',1);
        return $this;
    }

    protected function filters(){
        return array(
            TRUE => array(
                array('trim')
            ),
            'url'=> array(
                array(array($this,'translate'))
            ),
        );
    }

    protected function translate($value = null)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $str = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $str;
    }

    protected $_primary_key = 'section_id';
    protected $_table_name = 'shop_section';

    protected $_table_columns_set = array('name','url','text');

    protected $_table_columns = array(
        'section_id' => array(
            'column_name' => 'section_id',
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
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'text' => array(
            'column_name' => 'text',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 1
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}