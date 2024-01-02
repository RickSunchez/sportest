<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Core\Entity\Gallery;

class GallerySitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'gallery';

    public function initUrls()
    {
        $galleries = Gallery::model()
            ->sort()
            ->where('site', '=', $this->site)
            ->active();
        if (count($this->options['no_cid'])) {
            $galleries->where('cid', 'not in', $this->options['no_cid']);
        }
        $result = $galleries->find_all();
        foreach ($result as $item) {
            $this->addUrl(
                $item->link(),
                $item->date_cr,
                self::CHANGE_MONTHLY
            );
        }
    }
}