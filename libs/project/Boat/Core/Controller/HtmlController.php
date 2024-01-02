<?php

namespace Boat\Core\Controller;

use CMS\Core\Entity\Page;
use Delorius\Application\UI\Controller;

class HtmlController extends Controller
{

    public function menuPagePartial()
    {
        $ids = $this->getSite('pageIds');
        $var['ids'] = $ids;
        $var['page'] = $page = new Page($ids[0]);
        if (!$page->loaded()) {
            return;
        }

        $pages = Page::model()
            ->where('pid', '=', $page->pk())
            ->active()
            ->sort()
            ->cached()
            ->find_all();
        if (count($pages) == 0) {
            return;
        }
        $var['pages'] = $pages;

        if (count($ids) >= 2) {
            $var['select_page_id'] = $select_page_id = $ids[1];
            $var['select_child_page_id'] = $select_child_page_id = $ids[2];
            $child_pages = Page::model()
                ->where('pid', '=', $select_page_id)
                ->active()
                ->sort()
                ->cached()
                ->find_all();
            $var['child_pages'] = $child_pages;
        }


        $this->response($this->view->load('html/_menu_page', $var));
    }


    /**
     * @Post
     */
    public function callbackRealtimeAction()
    {

        $post = $this->httpRequest->getRequest();


        if (is_work()) {

        }


    }

}