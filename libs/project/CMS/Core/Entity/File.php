<?php
namespace CMS\Core\Entity;

use Delorius\Core\DateTime;
use Delorius\Core\ORM;
use Delorius\Utils\Size;
use Delorius\Utils\Strings;

class File extends ORM
{
    /** @return string */
    public function link(){
        return link_to('doc_download_file',array('id'=>$this->pk()));
    }

    /**
     * @param null $direction
     * @return $this
     */
    public function sort($direction = 'DESC')
    {
        $this->order_by('count', $direction)->order_pk();
        return $this;
    }

    /**
     * @param ORM $orm
     * @return $this
     */
    public function whereByTargetType(ORM $orm){
        $this->where('target_type','=',$orm->table_name());
        return $this;
    }

    /**
     * @param $targetId
     * @return $this
     */
    public function whereByTargetId($targetId){
        if(is_array($targetId)){
            $this->where('target_id','IN',$targetId);
        }else{
            $this->where('target_id','=',$targetId);
        }
        return $this;
    }

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

    public function as_array()
    {
        $arr = parent::as_array();
        $arr['link'] = $this->link();
        $arr['file_size'] = $this->file_size();
        $arr['file_name'] = $this->file_name();
        $arr['file_title'] = $this->title ? $this->title : $this->file_name();
        $arr['created'] = DateTime::dateFormat($arr[$this->_created_column['column']], true);
        $arr['updated'] = DateTime::dateFormat($arr[$this->_updated_column['column']], true);
        return $arr;
    }

    protected function behaviors()
    {
        return array(
            'editFileBehavior' => 'CMS\Core\Behaviors\EditFileBehavior',
        );
    }

    protected function rules(){
        return array(
            'path'=>array(
                array(array($this,'existsFile'),array(':value'),'Файла не существует.'),
            ),
        );
    }

    protected $_primary_key = 'file_id';
    protected $_table_name = 'df_files';

    protected $_table_columns_set = array('path');

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_updated_column =  array(
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
        'target_id' => array(
            'column_name' => 'target_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'target_type' => array(
            'column_name' => 'target_type',
            'data_type' => 'varchar',
            'character_maximum_length' => 50,
            'collation_name' => 'utf8_general_ci',
        ),
        'count' => array(
            'column_name' => 'count',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' =>0
        ),
        'path' => array(
            'column_name' => 'path',
            'data_type' => 'varchar',
            'character_maximum_length' => 300,
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
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'date_edit' => array(
            'column_name' => 'date_edit',
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_default' =>0
        )
    );
}