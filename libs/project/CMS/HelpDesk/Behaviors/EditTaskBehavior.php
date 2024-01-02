<?php
namespace CMS\HelpDesk\Behaviors;

use CMS\HelpDesk\Entity\TaskMessage;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;

class EditTaskBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $model = TaskMessage::model();
        DB::delete($model->table_name())
            ->where('task_id', '=', $orm->pk())
            ->execute($model->db_config());
        $model->cache_delete();
    }

    /**
     * @param $value
     * @return bool
     */
    public function addMessage($value, $is_admin = 0)
    {

        if (!$this->getOwner()->loaded()) {
            return false;
        }

        try {
            $task = $this->getOwner();
            $orm = new TaskMessage($value[TaskMessage::model()->primary_key()]);
            $orm->values($value);
            $orm->is_admin = $is_admin;
            $task->read_user = $is_admin == 0 ? 1 : 0;
            $task->read_admin = $is_admin == 0 ? 0 : 1;
            $orm->task_id = $task->pk();
            $orm->save(true);

            $task->count_msg++;
            $task->save(true);

            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

}