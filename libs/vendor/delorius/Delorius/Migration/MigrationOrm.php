<?php
namespace Delorius\Migration;

use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Core\ORM;
use Delorius\DataBase\DataBase;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Strings;

class MigrationOrm extends Object implements IMigrationItem, IMigrationInsert
{
    /** @var  string */
    protected $className;
    /** @var  ORM */
    private $_orm;
    /** @var bool */
    protected $isChange = false;
    /** @var bool */
    protected $isCreateTable = false;
    /** @var bool */
    protected $isPk = false;
    /** @var array */
    protected $table_edit_columns = array();
    /** @var \Delorius\Migration\BuilderQuery */
    protected $render;
    /** @var array */
    protected $table_columns = array();
    /** @var string */
    protected $table_name;
    /** @var array */
    protected $table_show_columns = array();
    /** @var array */
    protected $insertData= array();


    public function __construct($className)
    {
        if (!class_exists($className)) {
            throw new Error('You must specify the class ORM = ' . $className);
        }
        $this->render = new BuilderQuery();
        $this->className = $className;
    }

    /**
     * @return DataBase
     */
    public function getDB()
    {
        return DataBase::instance(null,$this->getModel()->db_config());
    }

    /**
     * @return string
     */
    public function getName()
    {
        return Strings::lower($this->className);
    }

    /**
     * @return bool
     */
    public function isChange()
    {
        $this->init();
        return $this->isChange;
    }

    /**
     * @return array
     */
    public function getQuery()
    {
        if ($this->isChange && $this->isCreateTable) {
            return $this->render->renderCreateTable($this->table_name, $this->table_columns);
        } else if ($this->isChange && sizeof($this->table_edit_columns)) {
            return $this->render->renderEditTable($this->table_name, $this->table_edit_columns, $this->isPk);
        }
        return array();
    }

    /**
     * @param $insert
     * @return $this
     */
    public function insert($insert)
    {
        $this->insertData[] = $insert;
        return $this;
    }

    /**
     * @return bool
     */
    public function isEmptyTable()
    {
        return $this->getModel()->find_all()->count() == 0 ? true : false;
    }

    public function insetTable()
    {
        if (sizeof($this->insertData)) {
            foreach ($this->insertData as $insert) {
                try {
                    $orm = $this->getModel();
                    $orm->values($insert);
                    $orm->save();
                } catch (OrmValidationError $e) {
                    Environment::getContext()->getService('logger')->error($e->getErrorsMessage(),'InsertMigrationOrm');
                }
            }
        }
    }

    /**
     * @return ORM
     */
    public function getModel()
    {
        if (is_null($this->_orm)) {
            $className = $this->className;
            $this->_orm = new $className();
        }
        return clone $this->_orm;
    }

    protected function init()
    {
        $orm = $this->getModel();
        # поля из орм
        $this->table_name = $orm->table_name();
        # поля из орм
        $this->table_columns = $orm->table_columns();
        # не указаны поля
        if (0 == sizeof($this->table_columns)) {
            return;
        }

        if (!$orm->issetTable()) {
            $this->createTable();
            return;
        }
        # поля из базы
        $this->table_show_columns = $orm->list_columns();
        # не одинаковое кол-во полей
        foreach ($this->table_columns as $name => $valueORM) {
            if (!isset($this->table_show_columns[$name])) {
                $this->addColumn($name, $valueORM);
                continue;
            }

            $valueDB = $this->table_show_columns[$name];

            if ($valueDB['key'] == 'PRI' && $valueORM['key'] != 'PRI') {
                $valueDB['key'] = $valueORM['key'];
            }
            foreach ($valueORM as $field => $param) {
                if ($valueDB[$field] != $param) {
                    $this->updateColumn($name, $valueORM);
                    break;
                }
            }
            unset($this->table_show_columns[$name]);
        }
        unset($name, $field, $param, $valueDB, $valueORM);
        foreach ($this->table_show_columns as $name => $value) {
            $this->dropColumn($name);
        }
    }

    protected function addColumn($name, $value)
    {
        $this->isChange = true;
        $this->table_edit_columns[] = array(
            'type' => BuilderQuery::ADD,
            'name' => $name,
            'value' => $value
        );
    }

    protected function updateColumn($name, $value)
    {
        $this->isChange = true;
        # избавления от повтороно добавления ключей
        if ($value['key'] == $this->table_show_columns[$name]['key']) {
            unset($value['key']);
        }
        $this->table_edit_columns[] = array(
            'type' => BuilderQuery::UPDATE,
            'name' => $name,
            'value' => $value
        );
    }

    protected function dropColumn($name)
    {
        $this->isChange = true;
        $this->table_edit_columns[] = array(
            'type' => BuilderQuery::DROP,
            'name' => $name
        );
    }

    protected function createTable()
    {
        $this->isChange = true;
        $this->isCreateTable = true;
    }
}