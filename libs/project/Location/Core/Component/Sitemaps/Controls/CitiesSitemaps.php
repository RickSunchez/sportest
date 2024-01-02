<?php
namespace Location\Core\Component\Sitemaps\Controls;

use CMS\Core\Component\Sitemaps\Controls\BaseSitemaps;
use Location\Core\Entity\City;

class CitiesSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'cities';

    public function initUrls()
    {
        $cities = City::model()->select()->where('main', '=', 0)->active()->find_all();

        foreach ($cities as $item) {
            $this->addUrl(
                link_to('homepage_city', array('city_url' => $item['url'])),
                time(),
                self::CHANGE_MONTHLY
            );
        }
    }


}