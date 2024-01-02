<?php
namespace CMS\Admin\Controller;

use CMS\Admin\Entity\Admin;
use CMS\Core\Entity\Image;
use CMS\Core\Entity\Table;
use CMS\Core\Helper\Helpers;
use CMS\Core\Helper\ImageHelper;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Path;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Картинки #admin_image?action=list
 */
class ImageController extends Controller
{

    /**
     * @param $page
     * @throws \Delorius\Exception\Error
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $results = Image::model()
            ->select(DB::expr('COUNT(image_id) as count'), 'target_type')
            ->group_by('target_type')
            ->order_by('target_type')
            ->find_all();


        $tables = array();
        if (Table::model()->issetTable()) {
            $orms = Table::model()->select()->sort()->find_all();
            foreach ($orms as $table) {
                $tables[$table['id']] = $table['target_type'];
            }
        }
        $var['tables'] = $tables;

        $var['types'] = array();
        foreach ($results as $type) {
            $type['target_name'] = $tables[$type['target_type']];
            $var['types'][] = $type;
        }


        $images = Image::model()
            ->order_pk('desc')
            ->order_by('target_type')
            ->order_by('target_id');
        $get = $this->httpRequest->getQuery();


        if ($get['image_id']) {
            $images->where('image_id', '=', $get['image_id']);
        }

        if ($get['id']) {
            $images->whereByTargetId($get['id']);
        }

        if ($get['table_id']) {
            $images->where('target_type', '=', $get['table_id']);
        }

        $pagination = PaginationBuilder::factory($images)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(ADMIN_PER_PAGE)
            ->addQueries($get);


        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['images'] = array();
        foreach ($result as $item) {
            $var['images'][] = $item->as_array();
        }
        $var['get'] = $get;
        $this->response($this->view->load('cms/image/list', $var));

    }

    /**
     * @AddTitle Редактированя изображения
     * @Model(name=CMS\Core\Entity\Image,loaded=false)
     */
    public function editAction(Image $model)
    {
        if (!$model->loaded()) {
            $this->httpResponse->redirect(link_to('admin_image', array('action' => 'list')));
        }
        $var['image'] = $model->as_array();
        $this->response($this->view->load('cms/image/edit', $var));
    }


    /**
     * @Post
     * @Model(name=CMS\Core\Entity\Image)
     */
    public function posDataAction(Image $model)
    {
        $post = $this->httpRequest->getPost();
        try {
            if ($post['type'] == 'edit') {
                $model->pos = (int)$post['pos'];
            } else if ($post['type'] == 'up') {
                $model->pos++;
            } else if ($post['type'] == 'down') {
                $model->pos--;
            }
            $model->save(true);
            $result['image'] = $model->as_array();
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $image = new Image($post[Image::model()->primary_key()]);
        if ($image->loaded()) {
            try {
                $image->values($post);
                $image->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
                $result['image'] = $image->as_array();
            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        } else {
            $result['errors'][] = _t('CMS:Admin', 'Object not found');
        }
        $this->response($result);
    }


    /**
     * @Post
     * @Model(name=CMS\Core\Entity\Image)
     */
    public function mainDataAction(Image $model)
    {
        $status = $this->httpRequest->getPost('status', null);
        try {
            $model->setMainStatus($status);
            $model->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['image'] = $model->as_array();
        } catch (OrmValidationError $e) {
            $result['error'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\Core\Entity\Image)
     */
    public function deleteDataAction(Image $model)
    {
        $model->delete(true);
        $this->response(array('ok' => _t('CMS:Admin', 'Object is deleted')));
    }

    /**
     * @Post
     */
    public function addDataAction()
    {
        $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        $id = $this->httpRequest->getPost('id');
        $class = $this->httpRequest->getPost('class');
        $object = new $class($id);
        if ($object->loaded()) {
            $file = $this->httpRequest->getFile('file');
            $res = $object->addImage($file);
            if ($res) {
                $result = $res;
            }
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function setDataAction()
    {
        $result = array('error' => _t('CMS:Admin', 'Could not load file'));
        $id = $this->httpRequest->getPost('id');
        $class = $this->httpRequest->getPost('class');
        $object = new $class($id);
        if ($object->loaded()) {
            $file = $this->httpRequest->getFile('file');
            $res = $object->setImage($file);
            if ($res) {
                $result = $res;
            }
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\Core\Entity\Image)
     */
    public function refreshDataAction(Image $model)
    {
        list($width, $height, $type, $attr) = @getimagesize(DIR_INDEX . $model->normal);
        list($pre_width, $pre_height, $type, $attr) = @getimagesize(DIR_INDEX . $model->preview);

        try {
            $model->width = $width;
            $model->height = $height;
            $model->pre_width = $pre_width;
            $model->pre_height = $pre_height;
            $model->save();
            $result = array(
                'ok' => 'Данные обновлены',
                'image' => $model->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getMessage();
        }

        $this->response($result);

    }

    /**
     * @Post
     * @Model(name=CMS\Core\Entity\Image)
     */
    public function cropDataAction(Image $model)
    {
        $coords = $this->httpRequest->getPost('coords', false);
        try {

            if (!$coords) {
                throw new Error('Не указаны координаты');
            }

            $upload = new \Upload(DIR_INDEX . $model->normal);
            if (!$upload->uploaded) {
                throw new Error(_t('CMS:Admin', 'Could not load file'));
            }
            $file = new \SplFileObject(DIR_INDEX . $model->normal);

            //crop
            $upload->image_ratio_crop = true;
            $upload->image_crop = array($coords['y'], ($model->width - $coords['x2']), ($model->height - $coords['y2']), $coords['x']);
            $upload->Process($file->getPath());
            if (!$upload->processed) {
                throw new Error('Не удалось обрезать фото');
            }

            $path_preview = $upload->file_dst_pathname;
            //resize
            if ($coords['resize']) {
                $upload = new \Upload($path_preview);
                $upload->image_y = $model->pre_height;
                $upload->image_x = $model->pre_width;
                $upload->image_ratio_fill = true;
                $upload->image_convert = 'png';
                $upload->image_resize = true;
                $upload->Process($file->getPath());
                @unlink($path_preview);
                if (!$upload->processed) {
                    throw new Error('Не удалось обрезать фото');
                }
                $path_preview = $upload->file_dst_pathname;
            }

            list($width, $height, $type, $attr) = getimagesize($path_preview);

            if ($width <= 0 || $height <= 0) {
                @unlink($path_preview);
                throw new Error('Не удалось обрезать фото');
            }

            @unlink(DIR_INDEX . $model->preview);
            $model->preview = Path::localPath(DIR_INDEX, $path_preview);
            $model->pre_height = $height;
            $model->pre_width = $width;
            $model->save();

            $result = array(
                'ok' => 'Превью картинки изменено',
                'image' => $model->as_array()
            );


        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\Core\Entity\Image)
     */
    public function newDataAction(Image $model)
    {

        try {
            $file = $this->httpRequest->getFile('file');

            if (!$file->isImage()) {
                throw new Error('Хочу картинку!!!');
            }

            $watermark = $this->container->getParameters('watermark');

            $image = ImageHelper::download(
                $file,
                'new',
                IMAGE_WIDTH,
                IMAGE_HEIGHT,
                $model->pre_width,
                $model->pre_height,
                false,
                true,
                false,
                $watermark['normal']['path'] ? $watermark['normal']['path'] : false,
                $watermark['normal']['type'] ? $watermark['normal']['type'] : 'rand',
                $watermark['preview']['path'] ? $watermark['preview']['path'] : false,
                $watermark['preview']['type'] ? $watermark['preview']['type'] : 'rand',
                $model->normal,
                $model->preview
            );

            if (!$image) {
                throw new Error('Не удалось загрузить файл');
            }

            @unlink(DIR_INDEX . $model->normal);
            @unlink(DIR_INDEX . $model->preview);

            $model->values($image);
            $model->save();

            $result = $model->as_array();

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        } catch (Error $e) {
            $result['errors'][] = $e->getMessage();
        }

        $this->response($result);
    }


    /**
     * For CKEditor upload
     * @Post(false)
     */
    public function uploadDataAction()
    {
        $file = $this->httpRequest->getFile('upload');
        $callback = $this->httpRequest->getRequest('CKEditorFuncNum');
        try {
            if (!$file->isImage()) {
                throw new Error('Хочу картинку!!!');
            }

            $watermark = $this->container->getParameters('watermark');

            $image = ImageHelper::download(
                $file,
                'images',
                IMAGE_WIDTH,
                IMAGE_HEIGHT,
                IMAGE_PREVIEW_WIDTH,
                IMAGE_PREVIEW_HEIGHT,
                false,
                true,
                false,
                $watermark['normal']['path'] ? $watermark['normal']['path'] : false,
                $watermark['normal']['type'] ? $watermark['normal']['type'] : 'rand',
                $watermark['preview']['path'] ? $watermark['preview']['path'] : false,
                $watermark['preview']['type'] ? $watermark['preview']['type'] : 'rand'
            );

            if (!$image) {
                throw new Error('Не удалось загрузить файл');
            }

            $model = new Image();
            $model->values($image);
            $model->target_type = Helpers::getTableId(Admin::model()->table_name());
            $model->target_id = $this->user->getId();
            $model->save();

            $path = $image['normal'];

        } catch (Error $e) {
            $message = $e->getMessage();
        }


        $this->response(_sf('<script type="text/javascript">window.parent.CKEDITOR.tools.callFunction("{0}", "{1}", "{2}");</script>', $callback, $path, $message));

    }


    public function managerAction($page)
    {
        $this->layout('empty');
        $results = Image::model()
            ->select(DB::expr('COUNT(image_id) as count'), 'target_type')
            ->group_by('target_type')
            ->order_by('target_type')
            ->find_all();

        $tables = array();
        if (Table::model()->issetTable()) {
            $orms = Table::model()->select()->sort()->find_all();
            foreach ($orms as $table) {
                $tables[$table['id']] = $table['target_type'];
            }
        }
        $var['tables'] = $tables;

        $var['types'] = array();
        foreach ($results as $type) {
            $type['target_name'] = $tables[$type['target_type']];
            $var['types'][] = $type;
        }


        $images = Image::model()
            ->order_pk('desc')
            ->order_by('target_type')
            ->order_by('target_id');
        $get = $this->httpRequest->getQuery();


        if ($get['image_id']) {
            $images->where('image_id', '=', $get['image_id']);
        }

        if ($get['id']) {
            $images->whereByTargetId($get['id']);
        }

        if ($get['table']) {
            $id = Helpers::getTableId($get['table']);
            $images->where('target_type', '=', $id);
        }

        $pagination = PaginationBuilder::factory($images)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(30)
            ->addQueries($get);

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['images'] = array();
        foreach ($result as $item) {
            $var['images'][] = $item->as_array();
        }
        $var['get'] = $get;
        $this->response($this->view->load('cms/image/manager', $var));
    }
}