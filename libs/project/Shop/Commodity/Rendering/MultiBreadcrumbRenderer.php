<?php

namespace Shop\Commodity\Rendering;

use Delorius\Page\Breadcrumb\Controls\LinkControl;
use Delorius\Page\Breadcrumb\Rendering\DefaultBreadcrumbRenderer;
use Delorius\Page\Breadcrumb\BreadcrumbBuilder;


class MultiBreadcrumbRenderer extends DefaultBreadcrumbRenderer
{

    protected $counter = 0;
    /** @var array of HTML tags */
    protected $wrappers = array(
        'breadcrumbs' => array(
            'ol' => 'ol class="breadcrumb hListing"',
            'container' => 'div class="breadcrumb-list"',
        ),
        'item' => array(
            'container' => 'li',
            '.active' => 'active'
        )
    );

    /** @var  \Delorius\Page\Breadcrumb\BreadcrumbBuilder */
    protected $breadcrumb;

    protected $cats = array();
    protected $router;

    public function __construct($cats, $router)
    {
        $this->cats = $cats;
        $this->router = $router;
    }


    /**
     * Provides complete form rendering.
     * @param  \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @return string
     */
    public function render(BreadcrumbBuilder $breadcrumb)
    {
        if ($this->breadcrumb !== $breadcrumb) {
            $this->breadcrumb = $breadcrumb;
        }

        $html = '';
        if (count($this->cats)) {
            foreach ($this->cats as $category) {
                $s = '';
                $s .= $this->renderFirstElm();

                $parents = $category->getParents();
                foreach ($parents as $cat) {
                    $s .= $this->renderLink(new LinkControl($cat['name'], link_to($this->router, array('cid' => $cat['cid'], 'url' => $cat['url'])), $cat['name']));

                }
                $s .= $this->renderLink(new LinkControl($category->name, link_to($this->router, array('cid' => $category->pk(), 'url' => $category->url)), $category->name));
                $s .= $this->renderLastElm();
                $rBreadcrumbs = $this->getWrapper('breadcrumbs ol');
                $rBreadcrumbs->setHtml($s);
                $rBreadcrumbs->addAttributes(array('itemscope' => '', 'itemtype' => 'http://schema.org/BreadcrumbList'));
                $html .= $rBreadcrumbs->render();
            }
        }

        $container = $this->getWrapper('breadcrumbs container');
        $html = $container->setHtml($html);
        return $html;
    }


}