<?php
namespace Shop\Admin\Controller;

use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use CMS\Users\Entity\User;
use Delorius\Utils\Arrays;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Review;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Отзывы #admin_reviews?action=list
 */
class ReviewsController extends Controller
{
    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {
        $reviews = Review::model()->sort('DESC');

        $pagination = PaginationBuilder::factory($reviews)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(isset($get['step']) ? $get['step'] : ADMIN_PER_PAGE)
            ->addQueries(array('action' => 'list'))
            ->setRoute('admin_reviews');

        $reviews = $pagination->result();
        $var['reviews'] = $gids = $uids = array();
        foreach ($reviews as $review) {
            $gids[] = $review->goods_id;
            $uids[] = $review->user_id;
            $var['reviews'][] = $review->as_array();
        }

        if (sizeof($uids)) {
            $users = User::model()->whereUserIds($uids)->find_all();
            $var['users'] = Arrays::resultAsArrayKey($users, 'user_id', true) ;
        }
        if (sizeof($gids)) {
            $goods = Goods::model()->where('goods_id', 'in', $gids)->find_all();
            $var['goods'] = Arrays::resultAsArrayKey($goods, 'goods_id', true) ;
        }

        $var['pagination'] = $pagination;
        $this->response($this->view->load('shop/goods/reviews/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактирование комментария
     * @Model(name=Shop\Commodity\Entity\Review)
     */
    public function editAction(Review $model)
    {
        $var = array();
        $var['review'] = $model->as_array();
        $var['goods'] = Goods::model($model->goods_id);
        $var['user'] = User::model($model->user_id);
        $this->response($this->view->load('shop/goods/reviews/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $post = $post['review'];

        try {
            $vendor = new Review($post['review_id']);
            $vendor->values($post);
            $vendor->save();

            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['review'] = $vendor->as_array();

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
        $post = $this->httpRequest->getPost();
        $review = new Review($post['id']);
        if ($review->loaded()) {
            $review->delete(true);
        }
        $this->response(array('ok'));
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $model = new Review($post['id']);
        if ($model->loaded()) {
            $model->status = (int)$post['status'];
            $model->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else {
            $result['error'] = _t('CMS:Admin', 'Object not found');
        }
        $this->response($result);
    }

}