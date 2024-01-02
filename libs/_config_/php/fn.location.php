<?php

defined('DELORIUS') or die('access denied');

/**
 * @param $name
 * @param array $parameters
 * @param bool|true $absoluteUrl
 * @return mixed|string
 */
function link_to_city($name, array $parameters = array(), $absoluteUrl = true)
{
    $city = DI()->getService('city');
    $parameters['city_url'] = $city->getUrl();
    $link = link_to($name, $parameters, $absoluteUrl);
    if ($city->isDefault())
        $link = str_replace('/' . $city->getUrl(), '', $link);
    return $link;
}


/**
 * @param array $arr
 * @return string
 */
function link_to_city_array(array $arr = null, $absoluteUrl = true)
{
    if (!$arr)
        return '';
    $router = array_keys($arr);
    $router = array_shift($router);
    $attrs = array_values($arr);
    $attrs = array_shift($attrs);
    if (!$router) {
        $router = $attrs;
        $attrs = array();
    }
    $link = link_to_city($router, (array)$attrs, $absoluteUrl);
    return $link;
}

/**
 * @return string
 */
function homepage()
{
    $city = DI()->getService('city')->get();

    if (!$city['main']) {
        return link_to('homepage_city', array('city_url' => $city['url']));
    } else {
        return link_to('homepage');
    }
}

/**
 * @return \Location\Core\Model\CitiesBuilder
 */
function city_builder()
{
    return DI()->getService('city');

}

/**
 * @return \Delorius\Core\ORM|\Delorius\DataBase\Result
 * @throws \Delorius\Exception\Error
 */
function get_cities()
{
    static $cities = null;
    if ($cities == null) {
        $cities = \Location\Core\Entity\City::model()
            ->active()
            ->sort()
            ->select('name', 'name_2', 'name_3', 'name_4', 'id', 'url', 'main')
            ->cached()
            ->find_all();
    }
    return $cities;
}