<?php
namespace CMS\Core\Entity;

use CMS\Core\Helper\Helpers;
use Delorius\Core\ORM;

/**
 * Class Options
 * @package CMS\Core\Entity
 *
 * @property int $id Primary key
 * @property int $target_id Primary key to target object
 * @property string $target_name Name to target object
 * @property string $value Value (max=200)
 * @property string $name Name by opts (max=200)
 * @property string $code Code by opts (max=20)
 * @property int $pos Priority
 */
class Options extends ORM
{
    /**
     * @return $this
     */
    public function sort($direction = null)
    {
        $this->order_by('pos', $direction)->order_pk();
        return $this;
    }

    /**
     * @param ORM $orm
     * @return $this
     */
    public function whereByTargetType(ORM $orm)
    {
        $this->where('target_type', '=', Helpers::getTableId($orm));
        return $this;
    }

    /**
     * @param $targetId
     * @return $this
     */
    public function whereByTargetId($targetId)
    {
        if (is_array($targetId)) {
            $this->where('target_id', 'IN', $targetId);
        } else {
            $this->where('target_id', '=', $targetId);
        }
        return $this;
    }

    protected $_table_name = 'df_options';
    protected $_table_columns_set = array('target_id', 'target_type', 'value');

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
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
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'varchar',
            'character_maximum_length' => 600,
            'collation_name' => 'utf8_general_ci',
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 200,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
    );
}