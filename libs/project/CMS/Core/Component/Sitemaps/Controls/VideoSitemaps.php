<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Core\Entity\Video;

class VideoSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'video';

    public function initUrls()
    {
        $videos = Video::model()
            ->sort()
            ->site($this->site)
            ->active();
        if (count($this->options['no_cid'])) {
            $videos->where('cid', 'not in', $this->options['no_cid']);
        }
        $result = $videos->find_all();
        foreach ($result as $item) {
            $this->addUrl(
                $item->link(),
                $item->date_cr,
                self::CHANGE_MONTHLY
            );
        }
    }
}