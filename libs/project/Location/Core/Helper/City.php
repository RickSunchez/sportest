<?php

namespace Location\Core\Helper;

use Delorius\Core\Environment;

class City
{

    public static function isMainPage()
    {
        $domain = Environment::getContext()->getService('routing.domainRouter');
        $httpRequest = Environment::getContext()->getService('httpRequest');

        $options = $domain->match($httpRequest->getUrl()->getAbsoluteUrlNoQuery());
        $url = $httpRequest->getUrl();
        if (!empty($options['_scriptPath'])) {
            $url->setScriptPath($options['_scriptPath']);
        }

        $code = \Delorius\Utils\Strings::trim($url->getPathInfo(), '/');
        return self::issetByCode($code);
    }


    protected static function issetByCode($code)
    {
        $cache = Environment::getContext()->getService('cache')->derive('city');

        if (($result = $cache->load('city_urls_list')) === NULL) {

            $cities = \Location\Core\Entity\City::model()
                ->active()
                ->select('url')
                ->find_all();

            foreach ($cities as $city) {
                $result[$city['url']] = true;
            }

            $cache->save('city_urls_list', $result);

        }
        return $result[$code];
    }

}