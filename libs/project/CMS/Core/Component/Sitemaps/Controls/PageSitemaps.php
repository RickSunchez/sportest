<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Core\Entity\Page;

class PageSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'page';

    public function initUrls()
    {
        $pages = Page::model()
            ->active()
            ->site($this->site)
            ->sort()
            ->main(0)
            ->find_all();
        foreach ($pages as $page) {
            if ($page->link() != '/' && !$page->redirect)
                $this->addUrl(
                    $page->link(),
                    $page->date_edit ? $page->date_edit : $page->date_cr,
                    self::CHANGE_MONTHLY,
                    $page->pid == 0 ? 1 : 0.6
                );
        }
    }
}