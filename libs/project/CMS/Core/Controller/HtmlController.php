<?php
namespace CMS\Core\Controller;

use CMS\Core\Entity\Config\Analytics;
use CMS\Core\Entity\Config\Menu;
use CMS\Core\Entity\Image;
use CMS\Core\Entity\Slider;
use Delorius\Application\UI\Controller;
use Delorius\Utils\Arrays;

class HtmlController extends Controller
{

    /**
     * Подключения хлебных крошек
     */
    public function breadcrumbsPartial($name = null, $url = '/', $title = null, $isRoute = false)
    {
        if ('homepage' == $this->getRouterName()) {
            return;
        }
        if ($name == null) {
            $name = $title = _t('CMS:Core', 'Home');
        }
        if ($title == null) {
            $title = $name;
        }

        $breadCrumbs = $this->container->getService('breadCrumbs');
        $breadCrumbs->setFirstItem(
            $name,
            '/',
            $title,
            $isRoute
        );
        $this->response($breadCrumbs->render());
    }

    /**
     * flash = [ok,error,errors]
     */
    public function flashPartial()
    {
        if ($this->hasFlash('ok')) {
            $msg = $this->getFlash('ok');
            $this->response($this->view->load('cms/flash/ok', array('msg' => $msg)));
        } elseif ($this->hasFlash('info')) {
            $msg = $this->getFlash('info');
            $this->response($this->view->load('cms/flash/info', array('msg' => $msg)));
        } elseif ($this->hasFlash('error')) {
            $msg = $this->getFlash('error');
            $this->response($this->view->load('cms/flash/error', array('msg' => $msg)));
        } elseif ($this->hasFlash('errors')) {
            $msgs = $this->getFlash('errors');
            if (sizeof($msgs)) {
                $this->response($this->view->load('cms/flash/error', array('msg' => $msgs)));
            }
        }
    }

    /**
     * Вывод кода аналитики
     * @param $header
     * @throws \Delorius\Exception\Error
     */
    public function codePartial($header = false)
    {
        $domain = getHostParameter('_route');
        $analytics = Analytics::model()->select()->cached()->where('domain', '=', $domain)->find();
        $this->response($header === false ? $analytics['footer'] : $analytics['header']);
    }

    /**
     * @param $code
     * @param int $limit
     * @param $theme
     * @throws \Delorius\Exception\Error
     */
    public function sliderPartial($code, $limit = 0, $theme)
    {
        $sliders = Slider::model()
            ->whereByCode($code)
            ->sort()
            ->active()
            ->cached();

        if ($limit) {
            $sliders->limit($limit);
        }

        $result = $sliders->find_all();
        $ids = $var['sliders'] = array();
        foreach ($result as $item) {
            $var['sliders'][] = $item;
            $ids[] = $item->pk();
        }

        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Slider::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }
        $theme = $theme ? '_' . $theme : '';
        $var['code'] = $code;
        $this->response($this->view->load('html/slider/_slider_' . $code . $theme, $var));
    }

    public function menuPartial($code, $theme)
    {
        if (!$code) {
            return;
        }
        $menus = Menu::model()
            ->sort()
            ->select()
            ->active()
            ->cached()
            ->whereByCode($code)
            ->find_all();
        $ids = $var['menu'] = array();
        foreach ($menus as $item) {
            $item = Menu::mock($item);
            $var['menu'][$item->pid][] = $item;
            $ids[] = $item->pk();
        }
        if (sizeof($ids)) {
            $images = Image::model()
                ->select('preview','normal','target_id','image_id')
                ->whereByTargetId($ids)
                ->whereByTargetType(Menu::model())
                ->cached()
                ->find_all();
            $var['images'] = Arrays::resultAsArrayKey($images, 'target_id');
        }
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/menu/_menu_' . $code . $theme, $var));
    }
}