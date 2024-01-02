<?php
namespace Shop\Admin\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Country;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Страны #admin_country?action=list
 */
class CountryController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction()
    {
        $countries = Country::model()->cached()->sort()->find_all();
        $ids = $var['countries'] = $var['images'] = array();
        foreach ($countries as $item) {
            $var['countries'][] = $item->as_array();
            $ids[] = $item->pk();
        }

        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Country::model())
                ->find_all();
            $var['images'] = Arrays::resultAsArray($images);
        }
        $this->response($this->view->load('shop/goods/country/list', $var));
    }    

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $country = new Country($post[Country::model()->primary_key()]);
            $country->values($post);
            $country->save(true);

            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $countries = Country::model()->cached()->sort()->find_all();
        $result['countries'] = Arrays::resultAsArray($countries);
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $country = new Country($post['id']);
        if ($country->loaded()) {
            $country->delete(true);
        }
        $this->response(array('ok'));
    }

}