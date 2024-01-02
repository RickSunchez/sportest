<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\Table;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Exception\Error;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Миграция для ORM #admin_migration?action=index
 */
class MigrationController extends Controller
{
    /**
     * @var \Delorius\Migration\MigrationManager
     * @service migrationManager
     * @inject
     */
    public $migration;

    public function indexAction()
    {
        $is_table = Table::model()->issetTable();
        $items = $this->migration->getItems();
        $var['items'] = array();
        foreach ($items as $item) {
            $arr = array();
            $table_name = $item->getModel()->table_name();
            if ($is_table) {
                $arr['table_id'] = Helpers::getTableId($table_name,false);
            }
            $arr['object_name'] = $item->getModel()->object_name();
            $arr['table_name'] = $table_name;
            $arr['table_columns'] = $item->getModel()->table_columns();
            $arr['change'] = 0;
            $arr['query'] = array();
            $arr['empty'] = 0;
            try {
                $arr['list_columns'] = $item->getModel()->list_columns();
                $arr['isset_table'] = sizeof($arr['list_columns']) == 0 ? 0 : 1;
                if ($item->isChange()) {
                    $arr['change'] = 1;
                    $arr['query'] = $item->getQuery();
                } else {
                    $arr['empty'] = $item->isEmptyTable() ? 1 : 0;
                }
            } catch (Error $e) {
                $arr['list_columns'] = array();
                $arr['isset_table'] = 0;
                $arr['empty'] = 0;
            }
            $var['items'][] = $arr;
        }
        $this->response($this->view->load('cms/migration/index', $var));
    }

    /**
     * @AddTitle Старт
     */
    public function startAction()
    {
        $items = $this->migration->getItems();
        $var['items'] = array();
        foreach ($items as $item) {
            if ($item->isChange()) {
                $arrQuery = $item->getQuery();
                foreach ($arrQuery as $sql) {

                    try {
                        $query = DB::query(NULL, $sql);
                        $query->execute($item->getDB());
                        $var['items'][] = $sql;
                    } catch (Error $e) {
                        $var['items'][] = '<span style="color: red;">Error: ' . $e->getMessage() . '</span>';
                    }


                }
            }
            if ($item->isEmptyTable()) {
                $item->insetTable();
            }
        }

        $this->response($this->view->load('cms/migration/start', $var));
    }


    /**
     * @AddTitle Таблицы
     */
    public function tableAction()
    {
        $var['table'] = Table::model()->select()->sort()->find_all();
        $this->response($this->view->load('cms/migration/table', $var));
    }

    /**
     * @AddTitle Обновить таблицу
     */
    public function tableUpdateAction()
    {
        $list_tables = Table::model()->select()->sort()->find_all();
        $table = array();
        foreach ($list_tables as $item) {
            $table[$item['target_type']] = $item;
        }

        $items = $this->migration->getItems();

        $update = array();
        foreach ($items as $item) {
            if (!isset($table[$item->getModel()->table_name()])) {
                $update[] = $item->getModel()->table_name();
                $obj = new Table();
                $obj->target_type = $item->getModel()->table_name();
                $obj->save();
            }
        }
        $var['update'] = $update;
        $this->response($this->view->load('cms/migration/table_update', $var));
    }

    /**
     * @AddTitle Обновить данные в таблице по полю
     */
    public function tableUpgradeAction()
    {
        if ($this->httpRequest->isPost()) {
            $post = $this->httpRequest->getPost();

            $list_tables = Table::model()->select()->sort()->find_all();

            foreach ($list_tables as $item) {

                DB::update($post['table_name'])
                    ->value($post['table_column'], $item['id'])
                    ->where($post['table_column'], '=', $item['target_type'])
                    ->execute();
            }


        }


        $this->response($this->view->load('cms/migration/table_upgrade'));
    }

}