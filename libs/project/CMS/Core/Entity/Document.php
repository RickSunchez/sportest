<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;
use Delorius\Utils\Size;
use Delorius\Utils\Strings;

class Document extends ORM
{

    /**
     * @param int $precision
     * @return string
     */
    public function file_size($precision = 2)
    {
        return Size::formatBytes($this->size, $precision);
    }

    /**
     * @return string
     */
    public function file_name(){
        return substr(basename($this->path),11);
    }

    /**
     * @param $value
     * @return bool
     */
    public function existsFile($value = null)
    {
        if($value == null){
            $value = $this->path;
        }
        return file_exists(DIR_INDEX . $value) ? true : false;
    }

    public function as_array(){
        $arr = parent::as_array();
        $arr['file_size'] = $this->file_size();
        $arr['file_name'] = $this->file_name();
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'editObjectForCatalogBehavior' => 'CMS\Catalog\Behaviors\EditObjectForCatalogBehavior',
            'editFileBehavior' => 'CMS\Core\Behaviors\EditFileBehavior',
        );
    }

    protected function rules()
    {
        return array(
            'path' => array(
                array(array($this, 'existsFile'), array(':value'), 'Файла не существует.'),
            ),
        );
    }

    protected function filters()
    {
        return array(
            TRUE => array(
                array('trim')
            ),
            'code' => array(
                array(array($this, 'code'))
            )
        );
    }

    protected function code($value = null)
    {
        if ($this->code) {
            return $this->code;
        }
        $value = $value ? $value : Strings::random(9, '0-9a-zA-Z');
        return $value;
    }

    protected $_primary_key = 'file_id';
    protected $_table_name = 'df_file';
    protected $_table_columns_set = array('path','code');

    protected $_created_column = array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column = array(
        'column' => 'date_edit',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'file_id' => array(
            'column_name' => 'file_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'cid' => array(
            'column_name' => 'cid',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0,
        ),
        'count' => array(
            'column_name' => 'count',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' => 0
        ),
        'path' => array(
            'column_name' => 'path',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'title' => array(
            'column_name' => 'title',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'ext' => array(
            'column_name' => 'ext',
            'data_type' => 'varchar',
            'character_maximum_length' => 10,
            'collation_name' => 'utf8_general_ci',
        ),
        'size' => array(
            'column_name' => 'size',
            'data_type' => 'varchar',
            'character_maximum_length' => 20,
            'collation_name' => 'utf8_general_ci',
        ),
        'code' => array(
            'column_name' => 'code',
            'data_type' => 'varchar',
            'character_maximum_length' => 10,
            'collation_name' => 'utf8_general_ci',
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