<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\Callback;
use CMS\HelpDesk\Entity\Task;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;

class HtmlController extends Controller{

    public function breadcrumbsPartial(){
        $breadCrumbs = $this->container->getService('breadCrumbs');
        $breadCrumbs->setFirstItem(
            '<i class="glyphicon glyphicon-home"></i>',
            'admin',
            'Панель управления'
        );
        $this->response($breadCrumbs->render());
    }


    public function menuPartial(){
        $adminMenu = $this->container->getService('adminMenu');
        $this->response($adminMenu->render());
    }


    /**
     * @Admin
     */
    public function helpDeskPartial()
    {
        $task = Task::model();
        $result = DB::select(array(DB::expr('COUNT(task_id)'), 'count'))
            ->where('read_admin', '=', 0)
            ->from($task->table_name())
            ->cached('+ 5 minutes')
            ->execute($task->db_config());
        $count = $result->get('count');
        if($count){
            $var['count'] = $count;
            $this->response($this->view->load('html/_help_desk',$var));
        }
    }

    /**
     * @Admin
     */
    public function callbackPartial()
    {
        $callback = Callback::model();
        $result = DB::select(array(DB::expr('COUNT(callback_id)'), 'count'))
            ->where('date_finished', '=', 0)
            ->from($callback->table_name())
            ->cached('+ 5 minutes')
            ->execute($callback->db_config());
        $count = $result->get('count');
        if($count){
            $var['count'] = $count;
            $this->response($this->view->load('html/_callback',$var));
        }
    }

}