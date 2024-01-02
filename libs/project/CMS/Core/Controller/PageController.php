<?php
namespace CMS\Core\Controller;

use CMS\Core\Entity\Page;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Routing\DomainRouter;
use Delorius\Utils\Strings;
use Delorius\View\Browser;

class PageController extends Controller
{


    /**
     * @service site
     * @inject
     */
    public $site;

    /**
     * @var Browser
     * @service browser
     * @inject
     */
    public $browser;

    /**
     * Seleceted pages ids
     * @var array
     */
    static public $pageIds = array();

    /**
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var DomainRouter
     * @inject
     */
    public $domainRouter;


    public function indexAction($url)
    {
        $router = Helpers::getCurrentDomain();
        $pages = array();
        $list = Page::model()->where('site', '=', $router)
            ->select('url', 'pid', 'short_title', 'id')
            ->active()
            ->cached()
            ->find_all();
        foreach ($list as $item) {
            $pages[$item['url']] = $item;
        }

        $path = str_replace('.html', '', $url);
        $list = explode('/', $path);
        $pid = 0;
        $count = count($list) - 1;

        $pageIds = array();
        foreach ($list as $k => $_url) {
            if (!empty($pages[$_url]) && $pid == $pages[$_url]['pid']) {
                $pid = $pages[$_url]['id'];
                $p = $pages[$_url];
                $pageIds[] = $p['id'];

                if ($count != $k)
                    $this->breadCrumbs->addLink($p['short_title'], _sf('[[page:{0}]]', $p['id']), null, false);
                else
                    $this->breadCrumbs->setLastItem($p['short_title']);
            } else {
                throw new NotFound(_t('CMS:Core', 'Page not found'));
            }
        }

        $this->setSite('pageIds', $pageIds);

        $page = new Page($p['id']);
        if ($page->status == 0) {
            throw new NotFound(_t('CMS:Admin', 'Page off'));
        }

        if (!empty($page->redirect)) {
            $this->httpResponse->redirect($page->redirect);
        }


        if ($this->site->mobile && $this->browser->isMobile() && !$this->browser->isFullVersion()) {
            $this->site->mobile = $page->mobile ? $page->mobile : $this->site->mobile;
            $this->template($this->site->mobile);
        } else {
            $this->template($page->template_dir);
        }
        $this->layout($page->template_page);

        /** @var \CMS\Core\Component\Header\HeaderControl $header */
        $header = $this->getHeader();
        if ($page->keys) {
            $header->addKeywords($page->keys);
        }

        $var['page'] = $page;

        $property_og = $page->getOptions('og');
        $property = array();
        foreach ($property_og as $opt) {
            if ($opt->value) {
                $property[$opt->code . ':' . $opt->name] = Strings::escape($opt->value);
            }
        }

        $var['image'] = $image = $page->getImage();
        if (!isset($property['og:image']) && $image->loaded()) {
            $property['og:image'] = $image->normal;
        }

        if (!$property['og:title']) {
            $property['og:title'] = $page->title;
        }

        if (!$property['og:description']) {
            $property['og:description'] = $page->description;
        }

        $this->setMeta(null, array(
            'desc' => $page->description,
            'property' => $property
        ));

        $this->setGUID($page->pk());
        $this->getHeader()->setTitle($page->title);

        $theme = $page->prefix ? '_' . $page->prefix : '';
        $this->response($this->view->load('cms/page/show' . $theme, $var));
        $this->lastModified($page->date_edit ? $page->date_edit : $page->date_cr);

    }

    public function textPartial($id = null, $template = null, $absolute = false)
    {
        #search page
        $page = Page::model();
        if ($id == null) {
            $page->main()->site(getHostParameter('_route'));
        } else {
            $page->where($page->primary_key(), '=', $id);
        }
        $page->find();
        #end search page

        if (!$page->loaded() && $id == null) {
            $this->response(_t('CMS:Core', 'Specify Home page'));
        } elseif (!$page->loaded() && $id > 0) {
            $this->response(_t('CMS:Core', 'Page with the id="{0}" does not exist', $id));
        } else {
            if ($template == null) {
                $this->response($page->text);
            } else {
                $this->response($this->view->load($template, array('page' => $page), $absolute));
            }
        }
    }


    public function menuPartial($theme = null)
    {
        $var['pageIds'] = $pageIds = $this->getSite('pageIds');
        $parent = new Page($pageIds[0]);
        if (!$parent->loaded()) {
            return null;
        }
        $var['parent'] = $parent;
        $pages = Page::model()->cached()->active()->sort()->where('pid', '=', $parent->pk())->find_all();
        $var['pages'] = $pages;
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/page/_menu' . $theme, $var));
    }


    public function listPartial($pageId, $theme = null)
    {
        $page = new Page($pageId);
        if (!$page->loaded()) {
            return;
        }
        $var['page'] = $page;
        $pages = Page::model()->where('pid', '=', $page->pk())
            ->sort()
            ->active()
            ->find_all();

        $var['pages'] = $pages;
        $var['pageIds'] = $this->getSite('pageIds');
        $theme = $theme ? '_' . $theme : '';
        $this->response($this->view->load('cms/page/_list' . $theme, $var));
    }


}