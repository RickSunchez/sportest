<?php

namespace Boat\Store\Controller;

use CMS\Core\Entity\Page;
use Delorius\Application\UI\Controller;
use Delorius\Exception\NotFound;
use Delorius\Utils\Strings;
use Delorius\View\Browser;


class PageCityController extends Controller
{


    public function before()
    {

        if (!$this->httpRequest->getRequest('not_change_city')) {
            $city_url = $this->getRouter('city_url');
            if ($city_url == null) {
                $this->city->setDefault();
            } elseif (!$this->city->has($city_url)) {
                throw new NotFound('Город не найден');
            } else {
                $this->city->set($city_url);
            }

            $this->setGUID($this->city->getId());
        }
    }

    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var Browser
     * @service browser
     * @inject
     */
    public $browser;


    /**
     * @var \Location\Core\Model\CitiesBuilder
     * @service city
     * @inject
     */
    public $city;


    public function contactAction($city_url)
    {

        $pageId = $this->city->getAttr('cID');
        $pageId = $pageId ? $pageId : PAGE_ID_CONTACT;

        $this->pageLoad($pageId);
    }

    public function deliveryAction($city_url)
    {

        $pageId = $this->city->getAttr('cID');
        $pageId = $pageId ? $pageId : PAGE_ID_DELIVERY;

        $this->pageLoad($pageId);
    }

    public function menuPartial()
    {
        if ($this->city->isDefault()) {

            $contact_link = '[page:2]';
            $delivery_link = '[page:8]';

        } else {

            $url = $this->city->getUrl();
            $contact_link = link_to('page_contact', array('city_url' => $url));
            $delivery_link = link_to('page_delivery', array('city_url' => $url));
        }

        $this->response($this->view->load('html/_menu_top',
            array(
                'contact_link' => $contact_link,
                'delivery_link' => $delivery_link,
            )));
    }


    public function pageLoad($pageId)
    {
        $page = new Page($pageId);

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

        $this->breadCrumbs->setLastItem($page->short_title);

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

}