<?php
namespace Delorius\Migration;

use Delorius\Core\Object;
use Delorius\Utils\Strings;

class BuilderQuery extends Object
{
    // Query types
    const ADD = 1;
    const UPDATE = 2;
    const DROP = 3;

    /** @var bool */
    protected $isPk = false;

    /** @return array query mysql */
    public function renderCreateTable($nameTable, $columns)
    {
        $this->name = $nameTable;
        $arrQuery = array();
        $this->addQueryCreateTable($nameTable, $columns);
        foreach ($this->_query as $query) {
            $arrQuery[]= $query . '; ';
        }
        foreach ($this->_add as $query) {
            $arrQuery[]= 'ALTER TABLE `' . $this->name . '` ADD ' . $query . '; ';
        }
        return  $arrQuery;
    }

    /** @return array query mysql */
    public function renderEditTable($nameTable, $columns, $isPk)
    {
        $this->name = $nameTable;
        $this->isPk = $isPk;
        foreach ($columns as $column) {
            $this->addQueryField($column);
        }

        $arrQuery = array();
        foreach ($this->_query as $query) {
            $arrQuery[]= $query . '; ';
        }

        foreach ($this->_add as $query) {
            $arrQuery[]= 'ALTER TABLE `' . $this->name . '` ADD ' . $query . '; ';
        }
        return $arrQuery;
    }

    /** @var string Table name */
    protected $name;
    /** @var  array string add for query */
    private $_add = array();
    /** @var array query */
    private $_query = array();

    protected function builderField($value)
    {
        $s = ' ';
        // name
        $s .= '`' . $value['column_name'] . '`';
        //type
        $type = Strings::upper($value['data_type']);
        list($type, $attr) = explode(' ', $type);
        if ( // int , text , varchar
            isset($value['display']) ||
            isset($value['character_maximum_length'])
        ) {
            $type .= '(' . (isset($value['display']) ? $value['display'] : $value['character_maximum_length']) . ')';
        }else if( // enum
            sizeof($value['options'])
        ){
            $type .= '(\'' .implode("','",$value['options']) .'\')';
        }else if( // decimal
            isset($value['exact'])
        ){
            $type .= '('.$value['numeric_precision'].','.$value['numeric_scale'].')';
        }
        $type .= ' ' . $attr . ' ';
        $s .= ' ' . $type . ' ';
        //collation name
        $s .= ($value['collation_name'] ? ' COLLATE ' . $value['collation_name'] . ' ' : ' ');
        //is_nullable
        $s .= ($value['is_nullable'] ? ' NULL ' : ' NOT NULL');
        //default
        $s .= ( isset($value['column_default']) ? ' DEFAULT "' . $value['column_default'] . '" ' : ' ');
        // extra
        $s .= ($value['extra'] ? ' ' . Strings::upper($value['extra']) . ' ' : ' ');
        // key
        $s .= $this->setKeyField($value['column_name'], $value['key']);
        return $s;
    }

    protected function setKeyField($name, $value)
    {
        if ($value == 'PRI' && !$this->isPk) {
            if (!$this->isPk)
                return 'PRIMARY KEY';
            else
                return '';
        } else if ($value == 'MUL') {
            $this->_add[] = ' INDEX (`' . $name . '`) ';
            return '';
        } else if ($value == 'UNI') {
            $this->_add[] = ' UNIQUE (`' . $name . '`) ';
            return '';
        }
    }

    protected function addQueryField($column)
    {
        switch ($column['type']) {
            case self::ADD:
                $this->_query[] = 'ALTER TABLE `' . $this->name . '` ADD ' . $this->builderField($column['value']);
                break;
            case self::UPDATE:
                $this->_query[] = 'ALTER TABLE `' . $this->name . '` CHANGE `' . $column['name'] . '`  ' . $this->builderField($column['value']);
                break;
            case self::DROP:
                $this->_query[] = 'ALTER TABLE `' . $this->name . '` DROP  `' . $column['name'] . '` ';
                break;
        }
    }

    protected function addQueryCreateTable($name, $columns)
    {
        $s = '';
        #start table
        $s .= 'CREATE TABLE IF NOT EXISTS `' . $name . '` (';
        foreach ($columns as $name_column => $column) {
            $s .= $this->builderField($column) . ',';
        }
        $s = substr($s, 0, strlen($s) - 1);
        $s .= ')';
        #end table
        $this->_query[] = $s;
    }

} 