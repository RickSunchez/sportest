<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Core\Entity\Event;

class EventSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'event';

    public function initUrls()
    {
        $events = Event::model()
            ->sort()
            ->where('site', '=', $this->site)
            ->active();
        if (count($this->options['no_cid'])) {
            $events->where('cid', 'not in', $this->options['no_cid']);
        }
        $result = $events->find_all();
        foreach ($result as $item) {
            $this->addUrl(
                $item->link(),
                time(),
                self::CHANGE_NEVER
            );
        }
    }
}