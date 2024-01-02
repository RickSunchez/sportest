<?php
namespace Location\Core\Controller;

use Delorius\Exception\NotFound;

class HomeController extends \CMS\Core\Controller\HomeController
{
    /**
     * @var \Location\Core\Model\CitiesBuilder
     * @service city
     * @inject
     */
    public $city;

    public function indexAction($city_url = null)
    {
        if ($city_url == null) {
            $this->city->setDefault();
        } else {
            if (!$this->city->has($city_url)) {
                throw new NotFound('Город не найден');
            }
            $this->city->set($city_url);
        }
        $this->setGUID($this->city->getId());
        $this->setSite('city', $this->city->getDefault());

        parent::indexAction();
    }


}