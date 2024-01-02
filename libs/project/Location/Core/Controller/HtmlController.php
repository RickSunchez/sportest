<?php
namespace Location\Core\Controller;

use Delorius\Application\UI\Controller;

class HtmlController extends Controller
{
    /**
     * @var \Location\Core\Model\CitiesBuilder
     * @service city
     * @inject
     */
    public $city;

    /**
     * Подключения хлебных крошек
     */
    public function breadcrumbsPartial($_name = 'Главная', $_url = null, $_title = null, $isRoute = false)
    {
        if ('homepage' == $this->getRouterName() || 'homepage_city' == $this->getRouterName()) {
            return;
        }
        $city = $this->city->get();
        if (!$city['main']) {
            $_url = link_to('homepage_city', array('city_url' => $city['url']));
        } else {
            $_url = $_url ? $_url : '/';
        }

        $breadCrumbs = $this->container->getService('breadCrumbs');
        $breadCrumbs->setFirstItem(
            $_name ? $_name : $city['name'],
            $_url,
            $_title ? $_title : $city['name'],
            $isRoute
        );
        $this->response($breadCrumbs->render());
    }

}