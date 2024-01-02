<?php
namespace CMS\Cabinet\Controller;

use Delorius\Core\Common;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Store\Entity\Balance;
use Shop\Store\Entity\Bill;
use Shop\Store\Entity\Cashflow;

/**
 * @User
 */
class BalanceController extends BaseController
{
    public function before()
    {
        if (!defined('SHOP_STORE')) {
            die('Not include SHOP_STORE');
        }
        if (!defined('SHOP_PAYMENT')) {
            die('Not include SHOP_PAYMENT');
        }
        parent::before();
    }

    public function indexAction($page)
    {
        $this->breadCrumbs->setLastItem('Баланс');
        $balance = Balance::getByUserId($this->user->getId());
        $cashflow = Cashflow::model()->where('user_id', '=', $this->user->getId())->sort();
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($cashflow)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(50)
            ->addQueries($get)
            ->setRoute('cabinet_balance');
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['cashflow'] = Arrays::resultAsArray($pagination->result(), false);
        $var['balance'] = $balance;
        $var['config'] = Common::getConfig('Shop:Payment');
        $this->response($this->view->load('cms/user/balance', $var));
    }

    public function accountAction($page)
    {
        $this->breadCrumbs->setLastItem('Счета');
        $this->breadCrumbs->addLink('Баланс', 'cabinet_balance');
        $var = array();
        $accounts = Bill::model()
            ->currentUser()
            ->sort();
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($accounts)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(10)
            ->addQueries($get)
            ->setRoute('cabinet_account');
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['bills'] = Arrays::resultAsArray($pagination->result(), false);
        $this->response($this->view->load('cms/user/account', $var));
    }

    /**
     * @Post
     */
    public function accountDataAction()
    {
        $post = $this->httpRequest->getPost();
        try{
            $bill = new Bill();
            $bill->values($post);
            $bill->status = Bill::STATUS_NEW;
            $bill->save();
            $result['ok'] = _t('CMS:Admin','These modified');
        }catch (OrmValidationError $e)
        {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }
}