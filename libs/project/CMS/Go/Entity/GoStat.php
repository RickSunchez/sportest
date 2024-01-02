<?php
namespace CMS\Go\Entity;

use Delorius\Core\ORM;
use Delorius\Http\Url;
use Delorius\Utils\Valid;

class GoStat extends ORM {

    protected function filters(){
        return array(
            TRUE => array(
                array('trim')
            ),'url_ref'=> array(
                array(array($this,'url'))
            )
        );
    }

    protected function url($value){
        if(Valid::url($value)){
            $url = new Url($value);
            $this->domain = $url->getHost();
        }
        return $value;
    }

    protected $_primary_key = 'stat_id';
    protected $_table_name = 'df_go_stat';

    protected $_table_columns_set = array('ip','url_ref');

    protected $_created_column =  array(
        'column' => 'date_cr',
        'format' => TRUE,
    );

    protected $_table_columns = array(
        'stat_id' => array(
            'data_type' => 'int unsigned',
            'display' => 11,
            'column_name' => 'stat_id',
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'go_id' => array(
            'column_name' => 'go_id',
            'data_type' => 'int unsigned',
            'display' => 11,
        ),
        'ip' => array(
            'column_name' => 'ip',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',

        ),
        'url_ref' => array(
            'column_name' => 'url_ref',
            'data_type' => 'varchar',
            'character_maximum_length' => 400,
            'collation_name' => 'utf8_general_ci',

        ),
        'domain' => array(
            'column_name' => 'domain',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',

        ),
        'comment' => array(
            'column_name' => 'comment',
            'data_type' => 'varchar',
            'character_maximum_length' => 500,
            'collation_name' => 'utf8_general_ci',
        ),
        'is_mail' => array(
            'column_name' => 'is_mail',
            'data_type' => 'tinyint',
            'display' => 1,
            'column_default'=>0
        ),
        'date_cr' => array(
            'column_name' => 'date_cr',
            'data_type' => 'int',
            'display' => 11,
        ),
    );

}