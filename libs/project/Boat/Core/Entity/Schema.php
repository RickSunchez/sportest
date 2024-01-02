<?php

namespace Boat\Core\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Strings;
use Shop\Commodity\Entity\Vendor;

class Schema extends ORM
{


    /**
     * @return string
     */
    public function link()
    {
        return link_to('schema_show', array('id' => $this->pk(), 'url' => $this->url));
    }

    /**
     * @return $this
     */
    public function sort()
    {
        $this->order_by('date_cr', 'DESC')
            ->order_pk();
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


    protected function translate($value)
    {
        if ($value == null) {
            $value = $this->name;
        }
        $value = Strings::webalize(Strings::translit(Strings::trim($value)));
        return $value;
    }

    protected function setName($value)
    {
        if ($this->url == null) {
            $this->url = $value;
        }
        $this->url = Strings::webalize(Strings::translit(Strings::trim($this->url)));
        return $value;
    }

    public function as_array()
    {
        $arr = parent::as_array();

        $arr['vendor'] = Vendor::model()
            ->where('vendor_id', '=', $this->vid)
            ->select('vendor_id', 'name')
            ->find();

        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'metaDataBehavior' => array(
                'class' => 'CMS\Core\Behaviors\MetaDataBehavior',
                'title' => 'Деталировка {vendor.name} {name} в [city:name?v=2]',
            ),
            'imageBehavior' => array(
                'class' => 'CMS\Core\Behaviors\ImageBehavior',
                'path' => 'schema',
                'ratio_fill' => true,
                'preview_width' => 300,
                'preview_height' => 300,
            ),
        );
    }

    protected function rules()
    {
        return array(
            'name' => array(
                array('\\Delorius\\Utils\\Validators::isText', array(':value', 1, 500), 'Введите название'),
            ),
        );
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'url' => array(
                array(array($this, 'translate'))
            ),
            'name' => array(
                array(array($this, 'setName'))
            ),
        );
    }

    protected $_table_name = 'boat_schema';
    protected $_table_columns_set = array('name');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'status' => array(
            'column_name' => 'status',
            'data_type' => 'tinyint unsigned',
            'display' => 1,
            'column_default' => 0,
        ),
        'cid' => array( #cat id
            'column_name' => 'cid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'pid' => array( #product id
            'column_name' => 'pid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'vid' => array( #vendor id
            'column_name' => 'vid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'title' => array(
            'column_name' => 'title',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'name' => array(
            'column_name' => 'name',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'pos' => array(
            'column_name' => 'pos',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        )
    );
}