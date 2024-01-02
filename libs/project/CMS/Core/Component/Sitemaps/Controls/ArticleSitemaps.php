<?php

namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Core\Entity\Article;

class ArticleSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'article';

    public function initUrls()
    {
        $news = Article::model()
            ->where('site', '=', $this->site)
            ->active()
            ->order_by('date_edit', 'desc')
            ->order_by('date_cr');

        if (count($this->options['no_cid'])) {
            $news->where('cid', 'not in', $this->options['no_cid']);
        }
        $result = $news->find_all();
        foreach ($result as $item) {
            $this->addUrl(
                $item->link(),
                $item->date_edit ? $item->date_edit : $item->date_cr,
                self::CHANGE_NEVER
            );
        }
    }
}