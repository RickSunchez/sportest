<?php
namespace Delorius\Page\Breadcrumb\Rendering;


use Delorius\Page\Breadcrumb\Controls\LinkControl;
use Delorius\Page\DefaultRenderer;
use Delorius\Page\Breadcrumb\IBreadcrumbRenderer;
use Delorius\Page\Breadcrumb\BreadcrumbBuilder;
use Delorius\View\Html;


/**
 * Class DefaultBreadcrumbRenderer
 * @package Delorius\Page\Breadcrumb\Rendering
 */
class DefaultBreadcrumbRenderer extends DefaultRenderer implements IBreadcrumbRenderer
{

    protected $counter = 0;
    /** @var array of HTML tags */
    protected $wrappers = array(
        'breadcrumbs' => array(
            'container' => 'ol class="breadcrumb hListing"',
        ),
        'item' => array(
            'container' => 'li',
            '.active' => 'active'
        )
    );

    /** @var  \Delorius\Page\Breadcrumb\BreadcrumbBuilder */
    protected $breadcrumb;


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

        $s = '';
        $s .= $this->renderFirstElm();
        foreach ($this->breadcrumb->getControls() as $link) {
            $s .= $this->renderLink($link);
        }
        $s .= $this->renderLastElm();
        $breadcrumbs = $this->getWrapper('breadcrumbs container');
        $breadcrumbs->setHtml($s);
        $breadcrumbs->addAttributes(array('itemscope' => '', 'itemtype' => 'http://schema.org/BreadcrumbList'));
        return $breadcrumbs->render();
    }


    public function renderFirstElm()
    {
        $firstItem = $this->breadcrumb->getFirstItem();
        return $this->renderLink($firstItem);
    }

    public function renderLastElm()
    {
        $lastItem = $this->breadcrumb->getLastItem();
        if ($lastItem == null) return '';
        $elm = $this->getWrapper('item container');
        $elm->setHtml($lastItem->getCaption());
        $elm->addAttributes(array('class' => $this->getValue('item .active') . ' last'));
        return $elm->render();
    }

    public function renderLink(LinkControl $link)
    {
        $this->counter++;
        $elm = $this->getWrapper('item container');
        $a = Html::el('a');
        $a->setHtml(_sf('<span itemprop="name" >{0}</span><meta itemprop="position" content="{1}">', $link->getCaption(), $this->counter));
        $a->href($link->getUrl());
        $a->addAttributes(array(
            'itemprop' => 'item'
        ));
        $elm->addAttributes(array(
            'title' => $link->getTitle(),
            'class' => 'item',
            'itemscope' => '',
            'itemprop' => 'itemListElement',
            'itemtype' => 'http://schema.org/ListItem',
        ));
        $elm->setHtml($a);
        return $elm->render();
    }


}