<?php
namespace Delorius\Behaviors;

use Delorius\Core\Environment;
use Delorius\Core\ORM;

class ORMBehavior extends Behavior
{

    public function events()
    {
        return array(
            'onBeforeSave' => 'beforeSave',
            'onAfterSave' => 'afterSave',
            'onBeforeDelete' => 'beforeDelete',
            'onAfterDelete' => 'afterDelete',
            'onBeforeFind' => 'beforeFind',
            'onAfterFind' => 'afterFind'
        );
    }


    protected function beforeSave(ORM $orm)
    {
    }

    protected function afterSave(ORM $orm)
    {
    }

    protected function beforeDelete(ORM $orm)
    {
    }

    protected function afterDelete(ORM $orm)
    {
    }

    protected function beforeFind(ORM $orm)
    {
    }

    protected function afterFind(ORM $orm)
    {
    }

    public function __sleep()
    {
        $var = array();
        foreach (get_object_vars($this) as $name => $value) {
            if (is_scalar($value)) {
                $var[$name] = $name;
            }
        }
        return array_keys($var);
    }

    public function __wakeup()
    {
        Environment::getContext()->callInjects($this);
    }


}