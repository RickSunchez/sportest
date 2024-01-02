<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Config\Menu;
use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Меню сайта #admin_menu?action=list
 */
class MenuController extends Controller
{

    /** @AddTitle Список */
    public function listAction()
    {
        $var['menus'] = $this->getMenus();
        $ids = array();
        foreach ($var['menus'] as $menu) {
            $ids[] = $menu['id'];
        }
        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Menu::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArray($images);
        }

        $var['types'] = Arrays::dataKeyValue(Menu::getTypes());
        $this->response($this->view->load('cms/menu/list', $var));
    }

    public function deleteAction()
    {
        $menu_id = (int)$this->httpRequest->getPost('id');
        $result = array('error' => '', 'ok' => '');

        try {
            $menu = new Menu($menu_id);
            $register = $this->container->getService('register');
            $menu->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Удален пункт меню : [id]:[code]:[type_name]:[value]',
                    $orm
                );
            };
            if (!$menu->loaded()) {
                throw new Error('Нет такой пункто меню');
            }
            if ($menu->children) {
                throw new Error('Ошибка: есть дочерние меню');
            }
            $menu->delete(true);
            $result['ok'] = 'Готово';


        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        try {
            $menu = new Menu((int)$post['id']);
            $menu->values($post);
            $register = $this->container->getService('register');
            $menu->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Изменен пункт меню: menu=[id]',
                    $orm
                );
            };
            $menu->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['menus'] = $this->getMenus();
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    public function changePosDataAction()
    {
        $post = $this->httpRequest->getPost();
        $menu = new Menu((int)$post['id']);
        if ($menu->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $menu->pos = (int)$post['pos'];
                } else if ($post['type'] == 'up') {
                    $menu->pos = $menu->pos + 1;
                } else if ($post['type'] == 'down') {
                    $menu->pos = $menu->pos - 1;
                }
                $menu->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
                $result['menus'] = $this->getMenus();
            } catch (OrmValidationError $e) {
                $result['error'] = $e->getErrorsMessage();
            }
        }
        $this->response($result);
    }

    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $menu = new Menu($post['id']);
        if ($menu->loaded()) {
            try {
                $menu->status = (int)$post['status'];
                $menu->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
            } catch (OrmValidationError $e) {
                $result['error'] = $e->getErrorsMessage();
            }
        } else
            $result['error'] = 'Страница не найдена';
        $this->response($result);
    }

    /** @return array */
    private function getMenus()
    {
        $list = Menu::model()->order_by('code')->sort()->find_all();
        return Arrays::resultAsArray($list);
    }


}