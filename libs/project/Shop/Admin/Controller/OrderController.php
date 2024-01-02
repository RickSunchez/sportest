<?php
namespace Shop\Admin\Controller;

use CMS\Core\Entity\Options;
use CMS\Users\Entity\User;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use Shop\Store\Entity\Item;
use Shop\Store\Entity\Order;
use Shop\Store\Helper\OrderHelper;

/**
 * @Template (name=admin)
 * @Admin
 * @SetTitle Заказы #admin_order?action=list
 */
class OrderController extends Controller
{

    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /**
     * @AddTitle Список заказов
     */
    public function listAction($page, $number)
    {
        $orders = Order::model()->sort();

        if ($number) {
            $orders->whereNumber($number);
        }
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($orders)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE)
            ->addQueries($get)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_order');

        $result = $pagination->result();
        $var['options'] = $var['users'] = $var['orders'] = $idsUser = $idsOrder = array();
        foreach ($result as $order) {
            $var['orders'][] = $order->as_array();
            $idsOrder[] = $order->pk();
            if ($order->user_id) {
                $idsUser[] = $order->user_id;
            }
        }
        if (count($idsUser)) {
            $users = User::model()->whereUserIds($idsUser)->find_all();
            $var['users'] = Arrays::resultAsArrayKey($users, 'user_id', true);
        }
        if (count($idsOrder)) {
            $opts = Options::model()->select()->whereByTargetId($idsOrder)->whereByTargetType(Order::model())->find_all();

            foreach ($opts as $opt) {
                $var['options'][$opt['target_id']][] = $opt;
            }
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['status'] = Arrays::dataKeyValue(OrderHelper::getStatusId());
        $this->response($this->view->load('shop/order/list', $var));
    }

    /**
     * @AddTitle Редактирование заказа
     * @Model(name=Shop\Store\Entity\Order)
     */
    public function editAction(Order $model)
    {
        $var['order'] = $model->as_array();
        $var['options'] = Arrays::resultAsArray($model->getOptions());
        $var['user'] = $var['images'] = $var['goods'] = array();
        $goods = $model->getItems();
        foreach ($goods as $item) {
            $var['items'][] = $item->as_array();
        }
        if ($model->userId()) {
            $user = new User($model->userId());
            if ($user->loaded()) {
                $var['user'] = $user->as_array();
            }
        }
        $var['status'] = Arrays::dataKeyValue(OrderHelper::getStatusId());
        $this->response($this->view->load('shop/order/edit', $var));
    }


    /**
     * @Post
     * @Model(name=Shop\Store\Entity\Order,field=order_id)
     */
    public function deliveryDataAction(Order $model)
    {
        $post = $this->httpRequest->getPost();
        $orderCart = new \Shop\Store\Component\Cart\OrderCart($model);

        if ($post['delivery']['name'] || $post['delivery']['desc'] || $post['delivery']['label']) {
            $post['delivery']['is_active'] = true;
        }

        if (!$post['delivery']['name'] && !$post['delivery']['desc'] && $post['delivery']['value'] == 0) {
            $post['delivery']['is_active'] = false;
        }

        $orderCart->set('delivery', $post['delivery']);
        $orderCart->update();
        $model = $orderCart->getOwner();
        $this->response(array('order' => $model->as_array()));
    }


    /**
     * @Post
     * @Model(name=Shop\Store\Entity\Order,field=order_id)
     */
    public function discountDataAction(Order $model)
    {
        $post = $this->httpRequest->getPost();
        $orderCart = new \Shop\Store\Component\Cart\OrderCart($model);
        $orderCart->set('discount', $post['discount']);
        $orderCart->update();
        $model = $orderCart->getOwner();
        $this->response(array('order' => $model->as_array()));
    }

    /**
     * @AddTitle Статистика по заказам
     */
    public function statAction()
    {
        $get = $this->httpRequest->getQuery();

        if (count($get)) {
            $orders = Order::model();

            if ($get['start']) {
                $start = strtotime($get['start']);
                $orders->where('date_cr', '>=', $start);
            }
            if ($get['end']) {
                $end = strtotime($get['end']);
                $orders->where('date_cr', '<=', $end);
            }

            if ($get['email']) {
                $orders->where('email', '=', $get['email']);
            }

            $result = $orders->find_all();
            $stats = array();
            foreach ($result as $item) {
                $stats[$item->status]['value'] += $item->value;
                $stats[$item->status]['count'] += 1;
            }
            $var['stats'] = $stats;
        }
        $var['get'] = $get;
        $this->response($this->view->load('shop/order/stat', $var));
    }

    /**
     * @Post
     */
    public function updateDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $order = Order::model($post['id']);
            if ($order->loaded()) {
                $order->values($post);
                $order->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
                $result['order'] = $order->as_array();
            } else {
                $result['errors'][] = _t('CMS:Admin', 'Object not found');
            }
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function optionDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $option = Options::model($post['id']);
            if ($option->loaded()) {
                $option->values($post);
                $option->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
            } else {
                $result['errors'][] = _t('CMS:Admin', 'Object not found');
            }
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function itemDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $post['amount'] = $post['amount'] ? $post['amount'] : 1;
            $item = Item::model($post['id']);
            if ($item->loaded()) {
                $item->values($post);
                $item->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');

                $order = new Order($item->order_id);
                $orderCart = new \Shop\Store\Component\Cart\OrderCart($order);
                $orderCart->update();
                $order = $orderCart->getOwner();
                $result['order'] = $order->as_array();
            } else {
                $result['errors'][] = _t('CMS:Admin', 'Object not found');
            }
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function itemDeleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $item = Item::model($post['id']);
        $order_id = $item->order_id;
        try {
            if ($item->loaded()) {
                $item->delete();
                $order = new Order($order_id);
                $orderCart = new \Shop\Store\Component\Cart\OrderCart($order);
                $orderCart->update();
                $order = $orderCart->getOwner();
                $result['order'] = $order->as_array();
            } else {
                throw new Error(_t('CMS:Admin', 'Object not found'));
            }
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

}