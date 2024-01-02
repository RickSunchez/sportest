<?php
namespace Shop\Commodity\Controller;

use CMS\Users\Entity\User;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Review;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @User(isLoggedIn=false)
 */
class ReviewController extends Controller
{
    protected $page = 5;

    /*
     * @Ajax
     */
    public function getDataAction($page)
    {
        $post = $this->httpRequest->getPost();

        $reviews = Review::model()->goods($post['goods_id'])->active()->sort('DESC');
        $pagination = PaginationBuilder::factory($reviews)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->page)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_reviews');
        $reviews = $pagination->result();
        $result = array();
        $result['html'] = null;
        foreach ($reviews as $item) {
            $result['html'] .= $this->view->load('shop/goods/review/item', array(
                'item' => $item,
                'user' => User::model($item->user_id),
            ));
        }
        $result['page']   = $pagination->getNextPage();
        $result['get']    = $pagination->getNextCountItems();
        $result['remain'] = $pagination->getRemainItems();

        $this->response($result);
    }

    public function indexPartial(Goods $goods)
    {
        $reviews = Review::model()
            ->goods($goods->pk())
            ->active()
            ->sort('DESC');
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($reviews)
            ->setItemCount(false)
            ->setPage($get['page'])
            ->setItemsPerPage($this->page)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_reviews');

        $reviews = $pagination->result();
        $users_ids = array();
        foreach ($reviews as $item) {
            $users_ids[] = $item->userId();
        }
        $var['users'] = array();
        if (count($users_ids)) {
            $users = User::model()->whereUserIds($users_ids)->find_all();
            $var['users'] = Arrays::resultAsArrayKey($users, 'user_id');
        }
        $var['pagination'] = $pagination;
        $var['goods'] = $goods;
        $var['items'] = $reviews;
        $this->response($this->view->load('shop/goods/review/index', $var));
    }

    public function itemPartial($item, $user)
    {
        $this->response($this->view->load('shop/goods/review/index', array('item' => $item, 'user' => $user)));
    }

    public function listPartial($items, $users, $pagination)
    {
        $var['users'] = $users;
        $var['items'] = $items;
        $this->response($this->view->load('shop/goods/review/list', $var));
    }

    public function formPartial($goods)
    {
        if ($this->user->isLoggedIn()) {
            $this->response($this->view->load('shop/goods/review/form', array('goods' => $goods)));
        } else {
            $this->response($this->view->load('shop/goods/review/auth'));
        }
    }

    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        $model = new Review;
        try {

            $model->values($post)->save();

            $goods = Goods::model($model->goods_id);
            $user = User::model($this->user->getId());
            $result = array(
                'ok' => _t('Shop:Commodity', 'Review Added'),
                'html' => $this->view->load('shop/goods/review/item', array(
                        'item' => $model,
                        'user' => $user,
                    )),
                'votes' => $goods->votes,
                'rating' => $goods->rating,
            );

        } catch (OrmValidationError $e) {
            $result = array(
                'errors' => $e->getErrorsMessage()
            );
        }
        $this->response($result);
    }
}