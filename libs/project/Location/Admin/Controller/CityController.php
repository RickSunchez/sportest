<?php
namespace Location\Admin\Controller;

use CMS\Core\Component\Register;
use Location\Core\Entity\City;
use Location\Core\Entity\Country;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Location\Core\Entity\Metro;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Города #admin_city?action=list
 */
class CityController extends Controller
{

    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $cities = City::model()->selectCountry()->sort();
        $var['get'] = $get = $this->httpRequest->getQuery();

        if ($get['country_id']) {
            $country = new Country($get['country_id']);
            if ($country->loaded()) {
                $var['country'] = $country;
                $this->breadCrumbs->addLink($country->name, 'admin_city?action=list&country_id=' . $country->pk());
                $cities->whereCountry($get['country_id']);
            }
        }

        if ($get['city']) {
            $cities->where_open();
            $cities->or_where($cities->table_name() . '.name', 'like', '%' . $get['city'] . '%');
            $cities->or_where($cities->table_name() . '.name_2', 'like', '%' . $get['city'] . '%');
            $cities->or_where($cities->table_name() . '.name_3', 'like', '%' . $get['city'] . '%');
            $cities->or_where($cities->table_name() . '.name_4', 'like', '%' . $get['city'] . '%');
            $cities->where_close();
        }

        if (isset($get['status'])) {
            $cities->where('status', '=', $get['status']);
        }


        $pagination = PaginationBuilder::factory($cities)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(30)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_city');

        $var['pagination'] = $pagination;
        $var['cities'] = array();
        foreach ($pagination->result() as $item) {
            $var['cities'][] = $item;
        }

        $this->response($this->view->load('location/city/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить город
     */
    public function addAction()
    {
        $countries = Country::model()->select()->sort()->cached()->find_all();
        $var['countries'] = Arrays::resultAsArray($countries, false);
        $this->response($this->view->load('location/city/edit'));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать город
     * @Model(name=Location\Core\Entity\City)
     */
    public function editAction(City $model)
    {
        $var = array();
        $var['city'] = $model->as_array();
        $var['image'] = $model->getImage()->as_array();

        $var['fields'] = $fields = $this->container->getParameters('location.city');
        $options = Arrays::resultAsArrayKey($model->getOptions(Arrays::keys($fields)), 'code');
        foreach ($fields as $code => $name) {
            if (isset($options[$code])) {
                $var['options'][] = array(
                    'code' => $code,
                    'value' => $options[$code]['value'],
                );
            } else {
                $var['options'][] = array(
                    'code' => $code,
                    'value' => null
                );
            }
        }
        $countries = Country::model()->select()->sort()->cached()->find_all();
        $var['countries'] = Arrays::resultAsArray($countries, false);
        $metro = Metro::model()->whereCity($model->pk())->select()->sort()->cached()->find_all();
        $var['metro'] = Arrays::resultAsArray($metro, false);
        $this->response($this->view->load('location/city/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $city = new City($post['city'][City::model()->primary_key()]);
            $city->values($post['city']);
            $register = $this->container->getService('register');
            $city->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Город изменен: id=[id]',
                    $orm
                );
            };
            $city->save(true);


            if (isset($post['meta'])) {
                $meta = $city->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            if (isset($post['options']) && count($post['options'])) {
                $fields = $this->container->getParameters('location.city');
                $merge = array();
                foreach ($post['options'] as $opt) {
                    $merge[] = array(
                        'code' => $opt['code'],
                        'name' => $fields[$opt['code']],
                        'value' => $opt['value']
                    );

                }

                $city->mergeOptions($merge, false);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $city->pk()
            );

        } catch (OrmValidationError $e) {
            $result = array('errors' => $e->getErrorsMessage());
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $city = new City($post['id']);
        try {
            if (!$city->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->container->getService('register');
            $city->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Город удален: [name]',
                    $orm
                );
            };
            $city->delete(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function mainDataAction()
    {
        $post = $this->httpRequest->getPost();

        $city = new City($post['id']);
        if ($city->loaded()) {

            DB::update($city->table_name())
                ->value('main', 0)
                ->execute($city->db_config());

            $city->main = (int)$post['main'];
            $city->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $city = new City($post['id']);
        if ($city->loaded()) {
            $city->status = (int)$post['status'];
            $city->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


    public function statusAllAction($status)
    {
        $city = new City();

        DB::update($city->table_name())
            ->value('status', $status)
            ->execute($city->db_config());


        $this->httpResponse->redirect(link_to('admin_city', array('action' => 'list')));
    }

    /**
     * Metro
     * save|delete
     */

    /**
     * @Post
     */
    public function saveMetroDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $metro = new Metro($post['metro'][Metro::model()->primary_key()]);
            $metro->values($post['metro']);
            $metro->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'metro' => $metro->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteMetroDataAction()
    {
        $post = $this->httpRequest->getPost();
        $metro = new Metro($post['id']);
        try {
            if (!$metro->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));
            $metro->delete(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }


}