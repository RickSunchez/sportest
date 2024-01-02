<?php
namespace Location\Store\Component\Sitemaps\Controls;

use CMS\Core\Component\Sitemaps\Controls\BaseSitemaps;
use Location\Core\Entity\City;
use Shop\Catalog\Entity\Collection;

class CollectionCategorySitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'collection_category';

    public function initUrls()
    {
        $this->name .= '_city_' . $this->options['type_id'];
        $collections = Collection::model()
            ->select('id', 'url', 'date_cr', 'date_edit')
            ->where('type_id','=',$this->options['type_id'])
            ->active()
            ->order_by('date_edit','desc')
            ->order_by('date_cr')
            ->find_all();

        $cities = City::model()->sort()->active()->find_all();

        foreach ($cities as $city) {
            $params = array();
            if ($city->main) {
                $router = $this->options['default_router'];
            } else {
                $params['city_url'] = $city->url;
                $router = $this->options['router'];
            }
            foreach ($collections as $item) {
                $params['url'] = $item['url'];
                $params['id'] = $item['id'];
                $this->addUrl(
                    link_to($router, $params),
                    $item['date_edit'] ? $item['date_edit'] : $item['date_cr'],
                    self::CHANGE_MONTHLY,
                    $item['pid'] == 0 ? 1 : 0.7
                );
            }
        }
    }


}