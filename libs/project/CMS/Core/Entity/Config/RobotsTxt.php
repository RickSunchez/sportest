<?php
namespace CMS\Core\Entity\Config;

use Delorius\Core\ORM;

class RobotsTxt extends ORM
{

    protected $_primary_key = 'robots_id';
    protected $_table_name = 'df_config_robots';

    protected $_table_columns = array(
        'robots_id' => array(
            'column_name' => 'robots_id',
            'data_type' => 'int unsigned',
            'display' => 11,
            'extra' => 'auto_increment',
            'key' => 'PRI'
        ),
        'domain' => array(
            'column_name' => 'domain',
            'data_type' => 'varchar',
            'character_maximum_length' => 100,
            'collation_name' => 'utf8_general_ci',
        ),
        'value' => array(
            'column_name' => 'value',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        )
    );
}