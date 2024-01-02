<?php
namespace CMS\Core\Entity;

use Delorius\Core\ORM;
use Delorius\Core\DateTime;

class FileIndex extends ORM
{
    /**
     * @return string
     */
    public function file_name(){
        return basename($this->path);
    }

    /**
     * @param $value
     * @return bool
     */
    public function existsFile($value)
    {
        return file_exists(DIR_INDEX . $value) ? true : false;
    }

    public function as_array(){
        $arr = parent::as_array();
        $arr['file_name'] = $this->file_name();
        $arr['created'] = DateTime::dateFormat($arr['date_cr'], true);
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'editFileBehavior' =>array(
                'class'=>'CMS\Core\Behaviors\EditFileBehavior',
                'timer'=>false,
                'absolute'=>DIR_INDEX.'/',
                'dir'=>'/'
            ),

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

    protected $_primary_key = 'file_id';
    protected $_table_name = 'df_file_index';

    protected $_table_columns_set = array('path');

    protected $_created_column = array(
        'column' => 'date_cr',
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
        'path' => array(
            'column_name' => 'path',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
    );
}