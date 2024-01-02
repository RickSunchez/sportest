<?php
namespace Shop\Commodity\Entity;

use Delorius\Core\ORM;

/**
 * Class CollectionPackage
 * @package Shop\Commodity\Entity
 *
 * @property int $id Primary key
 * @property int $coll_id Collection primary key
 * @property string $name Name package
 * @property int $pos Position
 */
class CollectionPackage extends ORM
{
    /**
     * @param string $direction
     * @return $this
     */
    public function sort($direction = 'desc')
    {
        $this->order_by($this->table_name().'.pos', $direction)->order_pk();
        return $this;
    }

    protected $_table_name = 'shop_collection_package';
    protected $_table_columns_set = array('name');

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 196), 'Укажите название'),
            ),
        );
    }

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
        'coll_id' => array(
            'column_name' => 'coll_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}