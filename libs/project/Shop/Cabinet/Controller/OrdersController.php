<?php
namespace Shop\Cabinet\Controller;

use CMS\Cabinet\Controller\BaseController;
use CMS\Users\Entity\User;
use Delorius\Exception\ForbiddenAccess;
use Shop\Store\Entity\Order;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @User
 */
class OrdersController extends BaseController
{
    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    public $page = 20;

    /**
     * @AddTitle Мои заказы
     */
    public function listAction($page, $number)
    {
        $get = $this->httpRequest->getQuery();
        $user = new User($this->user->getId());
        $var['get'] = $get;
        $var['user'] = $user->as_array();
        $var['image'] = $user->getImage()->as_array();

        $orders = new Order();
        $orders->order_created('desc')
            ->and_where_open()
            ->where('user_id', '=', $user->user_id)
            ->or_where('email', '=', $user->email)
            ->and_where_close();

        if ($number) {
            $orders->whereNumber($number);
        }

        $pagination = PaginationBuilder::factory($orders)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->page)
            ->addQueries($get)
            ->setRoute('cabinet_order');

        $var['pagination'] = $pagination;
        $orders = $pagination->result();
        $var['orders'] = array();
        foreach ($orders as $order) {
            $arr = $order->as_array();
            $item = array(
                'status_name' => $arr['status_name'],
                'order_id' => $arr['order_id'],
                'user_id' => $arr['user_id'],
                'number' => $arr['number'],
                'status' => $arr['status'],
                'price' => $arr['price'],
                'created' => $arr['created'],
                'updated' => $arr['updated'],
            );
            array_push($var['orders'], $item);
        }

        $this->response($this->view->load('shop/orders/list', $var));
    }

    /**
     * @AddTitle Просмотр заказа
     * @Model(name=Shop\Store\Entity\Order)
     */
    public function viewAction(Order $model)
    {
        $this->breadCrumbs->addLink('Мои заказы', 'cabinet_order?action=list');
        $this->breadCrumbs->setLastItem('Просмотр заказа');
        if ($model->user_id != $this->user->getId() && $model->email != $this->user->get('email')) {
            throw new ForbiddenAccess('Доступ запрещен к даному заказу');
        }
        $var['order'] = $model;
        $var['form'] = $model->getOptions();
        $var['config'] = $model->getConfig();
        $var['items'] = $model->getItems();
        $OrderCart = new \Shop\Store\Component\Cart\OrderCart($model);
        $var['order_cart'] = $OrderCart;
        $this->response($this->view->load('shop/orders/show', $var));
    }

}