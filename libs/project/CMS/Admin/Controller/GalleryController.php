<?php

namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Gallery;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use CMS\Catalog\Entity\Category;


/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Галерея #admin_gallery?action=list
 */
class GalleryController extends Controller
{

    protected $tmp = 'cms/gallery';

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
        $var['get'] = $get = $this->httpRequest->getQuery();
        $galleries = Gallery::model()
            ->sort();

        if (Helpers::getDomains() && $get['domain']) {
            $galleries->where('site', '=', $get['domain']);
        } else {
            $galleries->where('site', '=', 'www');
        }

        if (isset($get['cid'])) {
            $cids = array($get['cid']);
            $categories = Category::model($get['cid'])->getChildren();
            if (!empty($categories)) {
                foreach ($categories as $cat) {
                    $cids[] = $cat->cid;
                }
            }
            $galleries
                ->where('cid', 'in', $cids);
        }

        $pagination = PaginationBuilder::factory($galleries)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get);

        $var['pagination'] = $pagination;
        $var['galleries'] = Arrays::resultAsArray($pagination->result());
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load($this->tmp . '/list', $var));
    }

    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $gallery = new Gallery($post['gallery_id']);
            $gallery->values($post);
            $register = $this->container->getService('register');
            $gallery->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Галерея изменена: id=[gallery_id]',
                    $orm
                );
            };
            $gallery->save(true);

            $result['ok'] = _t('CMS:Admin', 'Ready');
            $result['gallery'] = $gallery->as_array();
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }

        $this->response($result);
    }

    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $gallery = new Gallery($post['gallery_id']);
        try {
            if (!$gallery->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));
            $register = $this->container->getService('register');
            $gallery->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Галерея удалена: [name]',
                    $orm
                );
            };
            $gallery->delete(true);
            $result['ok'] = _t('CMS:Admin', 'Ready');
        } catch (Error $e) {
            $result['errors'][] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Создать галерею
     */
    public function addAction()
    {
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load($this->tmp . '/images', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @Model(name=CMS\Core\Entity\Gallery)
     * @throws \Delorius\Exception\NotFound
     */
    public function imagesAction(Gallery $model)
    {
        $this->breadCrumbs->setLastItem(_t('CMS:Admin', 'Gallery: {0}', $model->name));
        $images = $model->getImages();
        $var['images'] = Arrays::resultAsArray($images);
        $var['gallery'] = $model->as_array();
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load($this->tmp . '/images', $var));
    }

    /**
     * @Post
     */
    public function changePosDataAction()
    {

        $post = $this->httpRequest->getPost();
        $gallery = new Gallery($post['id']);
        if ($gallery->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $gallery->pos = (int)$post['pos'];
                } else if ($post['type'] == 'up') {
                    $gallery->pos++;
                } else if ($post['type'] == 'down') {
                    $gallery->pos--;
                }
                $gallery->save(true);
                $result['ok'] = _t('CMS:Admin', 'Ready');

                $arrRealty = Gallery::model()
                    ->order_pk()
                    ->find_all();
                $result['galleries'] = Arrays::resultAsArray($arrRealty);

            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        }
        $this->response($result);
    }

    /**
     * @param $id
     * @Model(name=CMS\Core\Entity\Gallery)
     */
    public function archiveAction(Gallery $model)
    {

        if (!extension_loaded('zip')) {
            throw new Error('You dont have ZIP extension');
        }

        $images = $model->getImages();
        if ($images->count()) {
            $zip = new \ZipArchive();
            $zip_name = time() . ".zip"; // имя файла
            if ($zip->open($zip_name, \ZipArchive::CREATE) !== TRUE) {
                throw new Error('Sorry ZIP creation failed at this time');
            }
            foreach ($images as $img) {
                $zip->addFile(DIR_INDEX . $img->normal, basename(DIR_INDEX . $img->normal));
            }
            $zip->close();
            if (!file_exists($zip_name)) {
                throw new Error('Please select file to zip');
            }
            $this->httpResponse->setContentType('application/zip');
            $this->httpResponse->setHeader('Content-Disposition', 'filename="' . $zip_name . '"');
            readfile($zip_name);
            unlink($zip_name);
        } else
            throw new Error('Please select file to zip');

    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $gallery = new Gallery($post['id']);
        if ($gallery->loaded()) {
            $gallery->status = (int)$post['status'];
            $gallery->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }

}