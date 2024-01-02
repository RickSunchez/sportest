<?php
namespace CMS\Go\Entity;

use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Utils\Strings;

class Go extends ORM {

    public static function hasHash($hash){
        return Go::model()->where('hash','=',$hash)->find()->loaded();
    }

    public function clearStatistics(){
        if(!$this->loaded())
            return false;

        return DB::delete(GoStat::model()->table_name())
            ->where('go_id', '=', $this->pk())
            ->execute($this->_db);
    }

    protected $_primary_key = 'go_id';
    protected $_table_name = 'df_go';

    protected $_table_columns_set = array('redirect','url');

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected function filters(){
        return array(
            TRUE => array(
                array('trim')
            ),
            'comment'=> array(
                array('\\Delorius\\Utils\\Strings::truncate',array(':value',496))
            )
            ,'url'=> array(
                array(array($this,'url'))
            )
        );
    }

    protected function url($value = null){
        if($value == null){
            $value = $this->generateHashUrl();
            $this->hash = md5($value);
        }
        return $value;
    }

    protected function generateHashUrl($hash = null){
        $hash = Strings::random(3,'0-9a-zA-Z');
        if(!Go::hasHash(md5($hash)))
            return $hash;
        else
            return $this->generateHashUrl();

    }

    public function behaviors()
    {
        return array(
            'editGoBehavior' => 'CMS\Go\Behaviors\EditGoBehavior',
        );
    }

    protected $_table_columns = array(
        'go_id' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'go_id',
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'hash' => array(
            'column_name' => 'hash',
            'data_type' => 'varchar',
            'character_maximum_length' => 32,
            'collation_name' => 'utf8_general_ci',

        ),
        'visit' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'visit',
            'column_default' =>0
        ),
        'redirect' => array(
            'column_name' => 'redirect',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',

        ),
        'url' => array(
            'column_name' => 'url',
            'data_type' => 'varchar',
            'character_maximum_length' => 10,
            'collation_name' => 'utf8_general_ci',

        ),
        'comment' => array(
            'column_name' => 'comment',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'short_title' => array(
            'column_name' => 'short_title',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'key' => array(
            'column_name' => 'key',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int',
            'display' => 11,
        ),
    );
}