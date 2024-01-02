<?php
namespace CMS\Core\Entity\Config;

use Delorius\Core\ORM;

class Analytics extends ORM
{
    protected $_table_name = 'df_analytics_code';

    protected $_table_columns = array(
        'id' => array(
            'column_name' => 'id',
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
        'footer' => array(
            'column_name' => 'footer',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        ),
        'header' => array(
            'column_name' => 'header',
            'data_type' => 'text',
            'collation_name' => 'utf8_general_ci',
        )
    );
}