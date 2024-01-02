<?php
namespace CMS\Cabinet\Controller;

use CMS\Catalog\Entity\Category;
use CMS\HelpDesk\Entity\Task;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Utils\Json;
use Shop\Store\Entity\Balance;


class HtmlController extends Controller
{

    public function menuPartial()
    {
        $cabinetMenu = $this->container->getService('cabinetMenu');
        $this->response($cabinetMenu->render());
    }

    /**
     * @User(isLoggedIn=false)
     */
    public function authPartial()
    {
        if ($this->user->isLoggedIn()) {
            $this->response($this->view->load('cms/authorized/_is_login', array('user' => $this->user->getIdentity())));
        } else {
            $this->response($this->view->load('cms/authorized/_is_not_login'));
        }

    }

    public function breadcrumbsPartial()
    {
        $breadCrumbs = $this->container->getService('breadCrumbs');
        $this->response($breadCrumbs->render());
    }

    /**
     * @User(isLoggedIn=false)
     */
    public function balancePartial()
    {
        if (defined('SHOP_STORE')) {
            $var['balance'] = Balance::getByUserId($this->user->getId());
            $this->response($this->view->load('html/_balance', $var));
        }
    }

    /**
     * @User(isLoggedIn=false)
     */
    public function helpDeskPartial()
    {
        $task = Task::model();
        $result = DB::select(array(DB::expr('COUNT(task_id)'), 'count'))
            ->where('user_id', '=', $this->user->getId())
            ->where('read_user', '=', 0)
            ->from($task->table_name())
            ->cached('+ 5 minutes')
            ->execute($task->db_config());
        $count = $result->get('count');
        if($count){
            $var['count'] = $count;
            $this->response($this->view->load('html/_help_desk',$var));
        }
    }

    public function catsJsonPartial($pid, $typeId = Category::TYPE_NEWS, $placeholder = 'Без вложений')
    {
        $list = $this->selectCategories($pid, $typeId, $placeholder);
        $this->response(Json::encode($list));
    }

    /************************* select catalog *****************/
    private $lvl = 0;
    private $categories = array();
    private $selectId = 0;
    private $list = array();

    private function selectCategories($selectId, $typeId = Category::TYPE_NEWS, $placeholder = 'Без вложений', $seporator = '-')
    {
        $this->selectId = $selectId;
        $this->categories = $this->getCategories($typeId);
        $this->list[] = array(
            'value' => 0,
            'disabled' => false,
            'object' => 0,
            'name' => $placeholder,
            'lvl' => $this->lvl,
            'selected' => false,
        );
        foreach ($this->categories as $category) {
            if ($category['pid'] == 0) {
                $disabled = $this->selectId == $category['cid'] ? true : false;
                $this->list[] = array(
                    'seporator' => str_pad('', $this->lvl, $seporator),
                    'value' => $category['cid'],
                    'disabled' => $disabled,
                    'object' => $category['object'],
                    'name' => $category['name'],
                    'lvl' => $this->lvl,
                    'selected' => $this->selectId == $category['cid'] ? true : false
                );
                $this->getOptionCatalog($category['cid'], $disabled, $seporator);
            }
        }
        return $this->list;
    }

    private function getOptionCatalog($parentId, $disabled = false, $seporator = '-')
    {
        ++$this->lvl;
        $categories = $this->getCategoryByParentId($parentId);
        if (sizeof($categories)) {
            foreach ($categories as $category) {
                $disabled = (($this->selectId == $category['cid']) || $disabled) ? true : false;
                $this->list[] = array(
                    'seporator' => str_pad('', $this->lvl, $seporator),
                    'value' => $category['cid'],
                    'disabled' => $disabled,
                    'object' => $category['object'],
                    'name' => $category['name'],
                    'lvl' => $this->lvl,
                    'selected' => $this->selectId == $category['cid'] ? true : false
                );
                $this->getOptionCatalog($category['cid'], $disabled, $seporator);
            }
        }
        --$this->lvl;
    }

    private function getCategoryByParentId($parentId)
    {
        $arr = array();
        foreach ($this->categories as $category) {
            if ($category['pid'] == $parentId) {
                $arr[] = $category;
            }
        }
        return $arr;
    }

    /**
     * @param $typeId
     * @return array
     */
    private function getCategories($typeId)
    {
        $list = Category::model()
            ->sort()
            ->cached()
            ->type($typeId)
            ->find_all();
        $arr = array();
        foreach ($list as $item) {
            $arr[] = $item->as_array();
        }
        return $arr;
    }

    /*************************end select catalog *****************/

}