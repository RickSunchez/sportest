<?php
namespace CMS\Admin\Controller;

use CMS\Core\Entity\Image;
use CMS\Core\Entity\Slider;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Слайдер #admin_slider?action=list
 */
class SliderController extends Controller
{

    /** @var int */
    protected $perPage = 20;

    /*
    * @AddTitle Список
    */
    public function listAction($page)
    {
        $get = $this->httpRequest->getQuery();
        $sliders = Slider::model();
        $pagination = PaginationBuilder::factory($sliders)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_slider');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['sliders'] = $ids = array();
        foreach ($result as $item) {
            $var['sliders'][] = $item->as_array();
            $ids[] = $item->pk();
        }
        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Slider::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArray($images);
        }
        $this->response($this->view->load('cms/slider/list', $var));
    }

    /**
     * @AddTitle Добавить слайдер
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     */
    public function addAction()
    {
        $this->response($this->view->load('cms/slider/edit'));
    }


    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать слайдер
     * @Model(name=CMS\Core\Entity\Slider)
     */
    public function editAction(Slider $model)
    {
        $var = array();
        $var['slider'] = $model->as_array();
        $var['image'] = $model->getImage()->as_array();
        $this->response($this->view->load('cms/slider/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $slider = new Slider($post['slider'][Slider::model()->primary_key()]);
            $slider->values($post['slider']);
            if (!$slider->code) $slider->code = 'top';
            $slider->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'slider' => $slider->as_array()
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }

        $this->response($result);
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $slider = new Slider($post['id']);
        if ($slider->loaded()) {
            $slider->status = (int)$post['status'];
            $slider->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $sliderDel = new Slider($post['id']);
        if ($sliderDel->loaded()) {
            $sliderDel->delete(true);
        }
        $result['ok'] = _t('CMS:Admin', 'These modified');
        $this->response($result);
    }

}