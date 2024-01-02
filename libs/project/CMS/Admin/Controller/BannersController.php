<?php
namespace CMS\Admin\Controller;

use CMS\Banners\Entity\Banner;
use CMS\Core\Component\Register;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;

/**
 *  @Template(name=admin)
 *  @Admin
 *  @SetTitle Баннеры #admin_banner
 */
class BannersController extends Controller{

    /** @AddTitle Список */
    public function listAction($page,$code){
        $banners = Banner::model()->sort();
        $get = $this->httpRequest->getQuery();
        if($code){
            $banners->whereByCode($code);
        }
        $pagination = PaginationBuilder::factory($banners)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_banner');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['banners'] = array();
        foreach ($result as $item) {
            $var['banners'][] = $item->as_array();
        }
        $this->response($this->view->load('cms/banners/list',$var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить баннер
     */
    public function addAction(){
        $var['types'] = Arrays::dataKeyValue(Banner::getTypes());
        $this->response($this->view->load('cms/banners/edit',$var));
    }


    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать баннер
     * @Model(name=CMS\Banners\Entity\Banner)
     */
    public function editAction(Banner $model)
    {
        $var = array();
        $var['banner'] = $model->as_array();
        $var['types'] = Arrays::dataKeyValue(Banner::getTypes());
        $this->response($this->view->load('cms/banners/edit', $var));

    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $banner = new Banner($post['banner_id']);
            $banner->values($post);
            $register = $this->container->getService('register');
            $banner->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Баннер изменён: id=[banner_id]',
                    $orm
                );
            };
            $banner->save(true);

            $result = array(
                'ok' =>  _t('CMS:Admin','These modified'),
                'id' => $banner->pk()
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
        $banner = new Banner($post['id']);
        try {
            if (!$banner->loaded())
                throw new Error(_t('CMS:Admin','Object not found'));

            $register = $this->container->getService('register');
            $banner->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Баннер удален: [name]',
                    $orm
                );
            };
            $banner->delete(true);
            $result['ok'] =  _t('CMS:Admin','These modified');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\Banners\Entity\Banner,field=banner_id)
     */
    public function changePosDataAction(Banner $model){
        $post = $this->httpRequest->getPost();
        try{
            $model->pos = $post['pos'];
            $model->save(true);
            $result['ok'] = _t('CMS:Admin','These modified');
        }catch (OrmValidationError $e){
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function uploadDataAction()
    {
        $result = array('error' => _t('CMS:Admin','Could not load file'));
        $banner_json = $this->httpRequest->getPost('banner');
        $banner_arr = json_decode($banner_json, true);
        $banner = new Banner($banner_arr['banner_id']);
        if ($banner->loaded()) {
            $file = $this->httpRequest->getFile('file');
            $res = $banner->setFile($file);
            if ($res) {
                $result['ok'] = _t('CMS:Admin','These modified');
                $result['error'] = null;
                $result['path'] = $banner->path;
                $result['width'] = $banner->width;
                $result['height'] = $banner->height;
                $result['type_id'] = $banner->type_id;
                $banner->save(true);
            }
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function switchDataAction(){
        $post = $this->httpRequest->getPost();
        $banner = new Banner($post['id']);
        if($banner->loaded()){
            $banner->status = (int)$post['status'];
            $banner->save(true);
            $result['ok'] = _t('CMS:Admin','These modified');
        }
        else
            $result['error'] = _t('CMS:Admin','Object not found');
        $this->response($result);
    }

}