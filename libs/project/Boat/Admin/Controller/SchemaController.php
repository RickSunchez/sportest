<?php

namespace Boat\Admin\Controller;

use Boat\Core\Entity\Note;
use Boat\Core\Entity\NoteItem;
use Boat\Core\Entity\Schema;
use CMS\Core\Component\Register;
use Delorius\Application\UI\Controller;
use Delorius\Core\Environment;
use Delorius\DataBase\DB;
use Delorius\Exception\NotFound;
use Delorius\Exception\OrmValidationError;
use Delorius\Http\FileUpload;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Path;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Vendor;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Схемы #admin_schema?action=list
 *
 */
class SchemaController extends Controller
{

    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $schemes = Schema::model()->order_created('DESC');
        $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($schemes)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(40)
            ->addQueries($get);

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['schemes'] = Arrays::resultAsArray($result);
        $this->response($this->view->load('boat/schema/list', $var));
    }


    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $schema = new Schema($post['schema'][Schema::model()->primary_key()]);
            $schema->values($post['schema']);
            $schema->save(true);

            #meta
            if (count($post['meta'])) {
                $meta = $schema->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'schema' => $schema->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $city = new Schema($post['id']);
        if ($city->loaded()) {
            $city->status = (int)$post['status'];
            $city->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


    public function deleteDataAction()
    {
        $id = (int)$this->httpRequest->getPost('id');
        $scheme = new Schema($id);
        if ($scheme->loaded()) {

            $notes = Note::model()->where('sid', '=', $scheme->pk())->find_all();
            $ids = array();
            foreach ($notes as $note) {
                $ids[] = $note->pk();
                $note->delete(true);
            }

            if (count($ids)) {
                $item = NoteItem::model();
                DB::delete($item->table_name())->where('nid', 'in', $ids)->execute($item->db_config());
            }

            $scheme->delete(true);
        }
        $this->response(array('ok' => 'Удалено'));
    }


    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить схему
     */
    public function addAction()
    {
        $var = array();
        $vendors = Vendor::model()->cached()->sort()->find_all();
        $var['vendors'] = Arrays::resultAsArray($vendors);
        $this->response($this->view->load('boat/schema/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать схему
     * @Model(name=Boat\Core\Entity\Schema)
     */
    public function editAction(Schema $model)
    {
        $var = array();
        $var['schema'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();
        if ($model->pid) {
            $var['product'] = Goods::model($model->pid)->as_array();
        }
        $vendors = Vendor::model()->cached()->sort()->find_all();
        $var['vendors'] = Arrays::resultAsArray($vendors);
        $this->response($this->view->load('boat/schema/edit', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать узел
     * @Model(name=Boat\Core\Entity\Note)
     */
    public function noteAction(Note $model)
    {

        $var = array();

        $schema = new Schema($model->sid);
        if (!$schema->loaded()) {
            throw new NotFound();
        }
        $this->breadCrumbs->addLink($schema->name, _sf('admin_schema?action=edit&id={0}', $schema->pk()));

        $var['note'] = $model->as_array();
        $var['schema'] = $schema->as_array();
        $var['image'] = $model->getImage()->as_array();
        $var['meta'] = $model->getMeta()->as_array();

        $this->response($this->view->load('boat/schema/note', $var));
    }

    /**
     * @Model(name=Boat\Core\Entity\Schema)
     */
    public function copyDataAction(Schema $model)
    {
        $post = $this->httpRequest->getPost();
        $schema = new Schema($post['id']);
        if ($schema->loaded()) {


            try {
                $new_schema = new Schema();
                $new_schema->values(array(
                    'status' => 0,
                    'cid' => $schema->cid,
                    'pid' => $schema->pid,
                    'vid' => $schema->vid,
                    'url' => $schema->url,
                    'title' => $schema->title,
                    'name' => $schema->name . ' (копия)',
                ));
                $new_schema->save(true);

                $image_path = $this->getPathImage($schema->getImage()->normal);
                $new_schema->setImagePath($image_path);

                #notes
                $notes = Note::model()
                    ->where('sid', '=', $model->pk())
                    ->sort()
                    ->find_all();

                $did = $arr['notes'] = $arr['items'] = array();

                foreach ($notes as $note) {
                    $did[] = $note->pk();

                    $ar_n = $note->as_array();
                    $ar_n['image'] = $note->getImage()->normal;
                    $ar_n['image_temp'] = $this->getPathImage($note->getImage()->normal);

                    $arr['notes'][] = $ar_n;
                }

                if (count($did)) {
                    $items = NoteItem::model()
                        ->where('nid', 'in', $did)
                        ->select()
                        ->sort()
                        ->find_all();

                    foreach ($items as $item) {
                        $arr['items'][$item['nid']][] = $item;
                    }

                }

                if (count($arr['notes'])) {
                    foreach ($arr['notes'] as $note) {
                        $new_note = new Note();
                        $new_note->values(array(
                            'status' => $note['status'],
                            'sid' => $new_schema->pk(),
                            'title' => $note['title'],
                            'name' => $note['name'],
                            'pos' => $note['pos']
                        ));
                        $new_note->save(true);
                        $new_note->setImagePath($note['image_temp']);

                        if (count($arr['items'][$note['id']])) {
                            foreach ($arr['items'][$note['id']] as $item) {
                                $new_item = new NoteItem();
                                $new_item->values(array(
                                    'status' => $item['status'],
                                    'nid' => $new_note->pk(),
                                    'number' => $item['number'],
                                    'pid' => $item['pid'],
                                    'article' => $item['article'],
                                    'name' => $item['name'],
                                    'pos' => $item['pos'],
                                ));
                                $new_item->save(true);
                            }
                        }


                    }
                }

                $result = array(
                    'ok' => _t('CMS:Admin', 'These modified'),
                    'schema' => $new_schema->as_array(),
                    'arr' => $arr
                );
            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }

        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }

    protected function getPathImage($path)
    {
        $image = @file_get_contents(DIR_INDEX . $path);
        if ($image === false) {
            return null;
        }
        $upload = Environment::getContext()->getParameters('path.upload');
        $path_info = pathinfo($path);
        $file = $upload . '/temp/' . md5(date('d.m.Y H:i')) . '.' . $path_info['extension'];
        FileSystem::write($file, $image);
        return $file;
    }


    /**
     * @Model(name=Boat\Core\Entity\Schema)
     */
    public function loadNoteDataAction(Schema $model)
    {

        $items = Note::model()
            ->where('sid', '=', $model->pk())
            ->select()
            ->sort()
            ->find_all();

        $ids = $result['notes'] = array();
        foreach ($items as $item) {
            $ids[] = $item['id'];
            $result['notes'][] = $item;
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function saveNoteDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $note = new Note($post['note'][Note::model()->primary_key()]);
            $note->values($post['note']);
            $note->save(true);

            #meta
            if (count($post['meta'])) {
                $meta = $note->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'note' => $note->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Model(name=Boat\Core\Entity\Note)
     */
    public function loadNoteItemsDataAction(Note $model)
    {

        $items = NoteItem::model()
            ->where('nid', '=', $model->pk())
            ->select()
            ->sort()
            ->find_all();

        $pids = $result['items'] = array();
        foreach ($items as $item) {
            $pids[] = $item['pid'];
            $result['items'][] = $item;
        }

        if (count($pids)) {
            $result['products'] = array();
            $products = Goods::model()->select('goods_id', 'name', 'article')->where('goods_id', 'in', $pids)->find_all();
            foreach ($products as $item) {
                $result['products'][$item['goods_id']] = $item;
            }
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function saveNoteItemDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $note = new NoteItem($post['note'][NoteItem::model()->primary_key()]);
            $note->values($post['note']);
            $note->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'note' => $note->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }


    /**
     * @Post
     */
    public function deleteNoteItemDataAction()
    {
        $id = (int)$this->httpRequest->getPost('id');
        $item = new NoteItem($id);
        if ($item->loaded()) {
            $item->delete(true);
        }
        $this->response(array('ok' => 'Удалено'));
    }

    /**
     * @Post
     */
    public function deleteNoteDataAction()
    {
        $id = (int)$this->httpRequest->getPost('id');
        $note = new Note($id);
        if ($note->loaded()) {
            $note->delete(true);
            $item = NoteItem::model();
            DB::delete($item->table_name())->where('nid', '=', $id)->execute($item->db_config());
        }
        $this->response(array('ok' => 'Удалено'));
    }


}