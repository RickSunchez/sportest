<?php
namespace Location\Store\Component\Sitemaps\Collections;

use CMS\Core\Component\Sitemaps\Collections\BaseCollectionSitemaps;
use CMS\Core\Component\Sitemaps\Collections\CommonSitemaps;
use Delorius\Utils\Arrays;
use Location\Core\Entity\City;
use Shop\Commodity\Entity\Goods;

class GoodsCollectionSitemaps extends BaseCollectionSitemaps
{
    /**
     * type_id
     * default_router
     * router
     * site
     */


    /** @var string */
    protected $name = 'product_location';


    public function initUrls()
    {
        $this->name .= '_' . $this->options['type_id'];

        $cities = City::model()->sort()->active()->find_all();

        $goods = Goods::model()
            ->select('goods_id', 'url', 'date_cr', 'date_edit')
            ->ctype($this->options['type_id'])
            ->active()
            ->is_amount()
            ->order_by('date_edit', 'desc')
            ->order_by('date_cr')
            ->find_all();

        $goods = Arrays::resultAsArray($goods, false);


        foreach ($cities as $key => $city) {
            $params = array();
            if ($city->main) {
                $router = $this->options['default_router'];
            } else {
                $params['city_url'] = $city->url;
                $router = $this->options['router'];
            }

            $sitemaps = new CommonSitemaps(array(
                'site' => 'location-goods',
                'domain' => $this->site,
                'name' => $key
            ));

            foreach ($goods as $item) {

                $params['url'] = $item['url'];
                $params['id'] = $item['goods_id'];

                $sitemaps->addUrl(
                    link_to($router, $params),
                    $item['date_edit'] ? $item['date_edit'] : $item['date_cr'],
                    self::CHANGE_MONTHLY,
                    0.7
                );

            }

            $bits = $sitemaps->createXML();
            if ($bits) {
                $this->addSitemaps($sitemaps);
            }
        }
    }


}