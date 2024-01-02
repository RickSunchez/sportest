<?php

namespace CMS\Admin\Controller;

use CMS\Core\Entity\Table;
use CMS\Core\Entity\Tags;
use CMS\Core\Entity\TagsObject;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Теги #admin_tags
 */
class TagsController extends Controller
{

    /** @AddTitle Список */
    public function listAction($page)
    {
        $tables = array();
        if (Table::model()->issetTable()) {
            $orms = Table::model()->select()->sort()->find_all();
            foreach ($orms as $table) {
                $tables[$table['id']] = $table['target_type'];
            }
        }
        $var['tables'] = $tables;


        $tags = Tags::model()->sort();
        $var['get'] = $get = $this->httpRequest->getQuery();

        if ($get['table_id']) {
            $tags->where('target_type', '=', $get['table_id']);
        }

        if ($get['table_name']) {
            $tags->where('target_type', '=', Helpers::getTableId($get['table_name']));
        }

        if ($get['name']) {
            $tags->whereName($get['name']);
        }

        $pagination = PaginationBuilder::factory($tags)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(50)
            ->addQueries($get);

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['tags'] = array();
        foreach ($result as $item) {
            $arr = $item->as_array();
            $arr['target_name'] = $tables[$arr['target_type']];
            $var['tags'][] = $arr;
        }
        $this->response($this->view->load('cms/tags/list', $var));
    }

    /**
     * @AddTitle Редактировать тега
     * @Model(name=CMS\Core\Entity\Tags)
     */
    public function editAction(Tags $model)
    {
        $var = array();
        $var['tag'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $this->response($this->view->load('cms/tags/edit', $var));
    }


    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $tags = new Tags($post['tag'][Tags::model()->primary_key()]);
            $tags->values($post['tag']);
            $tags->save(true);

            if (count($post['meta'])) {
                $meta = $tags->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $tags->pk()
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $tags = new Tags($post['id']);
        try {
            if (!$tags->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));


            DB::delete(TagsObject::model()->table_name())
                ->where('tag_id', '=', $tags->pk())
                ->execute(TagsObject::model()->db_config());

            $tags->delete(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }


}