<?php
namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Shop\Store\Component\CBRAgent;
use Shop\Store\Entity\Currency;

/**
 * @Template(name=admin)
 * @Admin
 */
class CurrencyController extends Controller
{

    /**
     * @SetTitle Валюта #admin_currency?action=list
     * @AddTitle Список
     */
    public function listAction()
    {
        $currency = Currency::model()->cached()->order_pk()->find_all();
        $var['currency'] = Arrays::resultAsArray($currency);
        $var['config'] = $this->container->getParameters('shop.store');
        $this->response($this->view->load('shop/goods/currency/list', $var));
    }

    /**
     * @SetTitle Валюта #admin_currency?action=list
     * @AddTitle Добавить валюту
     */
    public function addAction()
    {
        $var['types'] = Arrays::dataKeyValue(Currency::getTypeDecimal());
        $this->response($this->view->load('shop/goods/currency/edit', $var));
    }

    /**
     * @SetTitle Валюта #admin_currency?action=list
     * @AddTitle Редактировать валюту
     * @Model(name=Shop\Store\Entity\Currency)
     */
    public function editAction(Currency $model)
    {
        $var['currency'] = $model->as_array();
        $var['types'] = Arrays::dataKeyValue(Currency::getTypeDecimal());
        $this->response($this->view->load('shop/goods/currency/edit', $var));
    }


    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $currency = new Currency($post['currency_id']);
            $currency->values($post);
            $currency->save(true);

            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['currency'] = $currency->as_array();

        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $id = $this->httpRequest->getPost('id');
        $currency = new Currency($id);
        if ($currency->loaded()) {
            $currency->delete(true);
        }
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function refreshDataAction()
    {
        $cbr = new CBRAgent();
        if ($cbr->load()) {
            $currency = Currency::model()->order_pk()->find_all();
            $result['currency'] = array();
            foreach ($currency as $item) {
                if (SYSTEM_CURRENCY != $item->code && $value = $cbr->get($item->code)) {
                    $item->values($value);
                    $item->save(true);
                }

                $result['currency'][] = $item->as_array();
            }
            $result['ok'] = 'Данные обновлены';
        } else {
            $result['error'] = 'Не удалось обратится к ЦБ';
        }
        $this->response($result);
    }


}