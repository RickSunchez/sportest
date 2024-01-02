<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Question;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Вопросы #admin_question
 */
class QuestionController extends Controller
{

    protected $tmp = 'cms/question';

    /**
     * @var \CMS\Core\Component\Register
     * @service register
     * @inject
     */
    public $register;

    /** @AddTitle Список */
    public function listAction($page)
    {
        $questions = Question::model()->sort();
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($questions)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_question');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['questions'] = array();
        foreach ($result as $item) {
            $var['questions'][] = $item->as_array();
        }
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load($this->tmp .= '/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить вопрос
     */
    public function addAction()
    {
        $this->response($this->view->load($this->tmp .= '/edit'));
    }


    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать вопрос
     * @Model(name=CMS\Core\Entity\Question)
     */
    public function editAction(Question $model)
    {
        $var = array();
        $var['question'] = $model->as_array();
        $this->response($this->view->load($this->tmp .= '/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $question = new Question($post['question'][Question::model()->primary_key()]);
            $question->values($post['question']);
            $register = $this->register;
            $question->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Редактирования вопроса',
                    $orm
                );
            };
            $question->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $question->pk()
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
        $question = new Question($post['id']);
        try {
            if (!$question->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->register;
            $question->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Вопрос удален: id=[Question_id]',
                    $orm
                );
            };

            $question->delete(true);
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
        $question = new Question($post['id']);
        if ($question->loaded()) {
            $question->status = (int)$post['status'];
            $question->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


}