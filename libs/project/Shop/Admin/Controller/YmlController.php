<?php

namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\FileSystem;
use Shop\Commodity\Component\YandexMarker\YmlGenerator;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\YmlGenerator as Yml;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle YML файл #admin_yml?action=list
 */
class YmlController extends Controller
{


    /**
     * @AddTitle Список
     */
    public function listAction()
    {
        $yml = Yml::model()->order_pk()->find_all();

        $var['yml'] = array();
        foreach ($yml as $item) {
            $var['yml'][] = $item->as_array();
        }

        $this->response($this->view->load('shop/yml/list', $var));
    }

    /**
     * @AddTitle Добавить
     */
    public function addAction()
    {
        $this->response($this->view->load('shop/yml/edit', $var));
    }

    /**
     * @AddTitle Редактировать
     * @Model(name=Shop\Commodity\Entity\YmlGenerator)
     */
    public function editAction(Yml $model)
    {
        $var['yml'] = $model->as_array();
        $this->response($this->view->load('shop/yml/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $yml = new Yml($post['yml'][Yml::model()->primary_key()]);
            $yml->values($post['yml']);
            $yml->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $yml->pk()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\YmlGenerator)
     */
    public function deleteDataAction(Yml $model)
    {
        $model->delete();
        $this->response(array('ok' => 1));
    }


    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\YmlGenerator)
     */
    public function deleteFileDataAction(Yml $model)
    {
        $model->deleteFileXML();
        $this->response(array('ok' => 1));
    }

    /**
     * @Post
     * @Model(name=Shop\Commodity\Entity\YmlGenerator)
     */
    public function generationDataAction(Yml $model)
    {
        @set_time_limit(0);
        @ignore_user_abort(1);
        $type_id = $model->ctype;
        $cids = $model->getConfig();
        if (count($cids)) {
            $gen = new YmlGenerator($type_id);
            $goods = Goods::model()
                ->select_array($gen->select)
                ->active()
                ->where('ctype', '=', $type_id)
                ->where('cid', 'in', $cids);
            if ($model->amount) {
                $goods->where('is_amount', '=', 1);
            }
            $result = $goods->find_all();
            $gen->create($result, $model->as_array());
            $this->response(array('ok' => 'Файл сгенерирован', 'yml' => $model->as_array()));
        } else {
            $this->response(array('error' => 'Не выбраны категории'));
        }

    }

    /**
     * @Post
     */
    public function cleanDataAction()
    {
        @set_time_limit(0);
        @ignore_user_abort(1);
        $dir = $this->container->getParameters('path.market');
        FileSystem::delete($dir);
        FileSystem::createDir($dir);
        $this->response(array('ok'));
    }

}