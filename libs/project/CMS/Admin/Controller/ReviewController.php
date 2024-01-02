<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Review;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Отзывы #admin_review
 */
class ReviewController extends Controller
{

    /**
     * @var \CMS\Core\Component\Register
     * @service register
     * @inject
     */
    public $register;

    /** @AddTitle Список */
    public function listAction($page)
    {
        $review = Review::model()->sort();
        $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($review)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get);

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['reviews'] = array();
        foreach ($result as $item) {
            $var['reviews'][] = $item->as_array();
        }

        $this->response($this->view->load('cms/review/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить отзыв
     */
    public function addAction()
    {
        $this->response($this->view->load('cms/review/edit'));
    }


    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать отзыв
     * @Model(name=CMS\Core\Entity\Review)
     */
    public function editAction(Review $model)
    {
        $var = array();
        $var['review'] = $model->as_array();
        $this->response($this->view->load('cms/review/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $review = new Review($post['review'][Review::model()->primary_key()]);
            $review->values($post['review']);
            $register = $this->register;
            $review->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Изменены данные отзыва',
                    $orm
                );
            };
            $review->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $review->pk()
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
        $review = new Review($post['id']);
        try {
            if (!$review->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->register;
            $review->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Отзыв удален: id=[review_id]',
                    $orm
                );
            };

            $review->delete(true);
            $result['ok'] = 'Отзыв удален';
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $review = new Review($post['id']);
        if ($review->loaded()) {
            $review->status = (int)$post['status'];
            $review->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


}