<?php
namespace CMS\Core\Entity;

use CMS\Core\Helper\Helpers;
use Delorius\Core\ORM;

/**
 * @property int $id Primary key
 * @property int $target_id Primary key to target object
 * @property string $target_type Name to target object
 * @property int $tag_id ID by tag
 * @property int $option Option by target object
 */
class TagsObject extends ORM
{

    /**
     * @param $id
     * @return $this
     */
    public function whereTagId($id)
    {
        $this->where($this->table_name() . '.tag_id', '=', $id);
        return $this;
    }

    /**
     * @param ORM $orm
     * @return $this
     */
    public function whereByTargetType(ORM $orm)
    {
        $this->where($this->table_name() . '.target_type', '=', Helpers::getTableId($orm));
        return $this;
    }

    /**
     * @param $targetId
     * @return $this
     */
    public function whereByTargetId($targetId)
    {
        if (is_array($targetId)) {
            $this->where($this->table_name() . '.target_id', 'IN', $targetId);
        } else {
            $this->where($this->table_name() . '.target_id', '=', $targetId);
        }
        return $this;
    }

    protected $_primary_key = 'id';
    protected $_table_name = 'df_tags_object';

    protected $_table_columns_set = array('target_id', 'target_type');

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'tag_id' => array(
            'column_name' => 'tag_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
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
        )
    );
}