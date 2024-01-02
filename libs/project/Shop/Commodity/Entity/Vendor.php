<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;

/**
 * Class Vendor
 * @package Shop\Commodity\Entity
 *
 * @property int $vendor_id Primary key
 * @property int $country_id Country primary key
 * @property string $name Name vendor
 * @property string $text About
 * @property int $pos Position
 */
class Vendor extends ORM
{
    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by($this->table_name() . '.pos', $direction)->order_pk();
        return $this;
    }

    protected function behaviors()
    {
        return array(
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => false
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'vendor',
                'ratio_fill' => true,
                'preview_width' => 250,
                'preview_height' => 250
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

    protected $_primary_key = 'vendor_id';
    protected $_table_name = 'shop_vendor';

    protected $_table_columns_set = array('name', 'url');

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            )
        );
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название'),
            ),
            'text' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 0, 1000), 'Описание не должно превышать больше 1000 символов'),
            ),
        );
    }

    protected $_table_columns = array(
        'vendor_id' => array(
            'column_name' => 'vendor_id',
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
        'name' => array(
            'column_name' => 'name',
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
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}