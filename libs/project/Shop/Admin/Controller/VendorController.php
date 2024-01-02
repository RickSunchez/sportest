<?php

namespace Shop\Admin\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Vendor;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Производители #admin_vendor?action=list
 */
class VendorController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $vendors = Vendor::model()->sort();
        $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($vendors)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_vendor');

        $ids = $var['vendors'] = $var['images'] = array();
        $result = $pagination->result();
        foreach ($result as $item) {
            $var['vendors'][] = $item->as_array();
            $ids[] = $item->pk();
        }

        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Vendor::model());
            $var['images'] = Arrays::resultAsArray($images->find_all());
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $this->response($this->view->load('shop/goods/vendor/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать
     * @Model(name=Shop\Commodity\Entity\Vendor)
     */
    public function editAction(Vendor $model)
    {
        $var['vendor'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();
        $this->response($this->view->load('shop/goods/vendor/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $vendor = new Vendor($post['vendor'][Vendor::model()->primary_key()]);
            $vendor->values($post['vendor']);
            $vendor->save(true);

            #meta
            if (count($post['meta'])) {
                $meta = $vendor->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'vendor' => $vendor->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\Vendor)
     */
    public function deleteDataAction(Vendor $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }
}