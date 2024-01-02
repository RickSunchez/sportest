<?php

namespace Location\Store\Component\Sitemaps\Collections;

use CMS\Core\Component\Sitemaps\Collections\BaseCollectionSitemaps;
use CMS\Core\Component\Sitemaps\Collections\CommonSitemaps;
use Delorius\Utils\Arrays;
use Location\Core\Entity\City;
use Shop\Catalog\Entity\Collection;

class CollectionCollectionSitemaps extends BaseCollectionSitemaps
{
    /**
     * type_id
     * default_router
     * router
     * site
     */


    /** @var string */
    protected $name = 'collection_location';


    public function initUrls()
    {
        $this->name .= '_' . $this->options['type_id'];

        $cities = City::model()->sort()->active()->find_all();

        $categories = Collection::model()
            ->select('id', 'url', 'date_cr', 'date_edit')
            ->where('type_id', '=', $this->options['type_id'])
            ->active()
            ->order_by('date_edit', 'desc')
            ->order_by('date_cr')
            ->find_all();

        $categories = Arrays::resultAsArray($categories, false);


        foreach ($cities as $key => $city) {
            $params = array();
            if ($city->main) {
                $router = $this->options['default_router'];
            } else {
                $params['city_url'] = $city->url;
                $router = $this->options['router'];
            }

            $sitemaps = new CommonSitemaps(array(
                'site' => 'location-collection',
                'domain' => $this->site,
                'name' => $key
            ));

            foreach ($categories as $cat) {

                $params['url'] = $cat['url'];
                $params['id'] = $cat['id'];

                $sitemaps->addUrl(
                    link_to($router, $params),
                    $cat['date_edit'] ? $cat['date_edit'] : $cat['date_cr'],
                    self::CHANGE_MONTHLY
                );

            }

            $bits = $sitemaps->createXML();
            if ($bits) {
                $this->addSitemaps($sitemaps);
            }
        }
    }


}