<?php
namespace CMS\Core\Component\Sitemaps\Controls;


use CMS\Core\Entity\News;

class NewsSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'news';

    public function initUrls()
    {
        $news = News::model()
            ->sort()
            ->where('site', '=', $this->site)
            ->active();
        if (count($this->options['no_cid'])) {
            $news->where('cid', 'not in', $this->options['no_cid']);
        }
        $result = $news->find_all();
        foreach ($result as $item) {
            $this->addUrl(
                $item->link(),
                $item->date_cr,
                self::CHANGE_NEVER
            );
        }
    }
}