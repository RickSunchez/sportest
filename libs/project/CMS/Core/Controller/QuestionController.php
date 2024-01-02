<?php
namespace CMS\Core\Controller;

use CMS\Core\Entity\Question;
use CMS\Mail\Model\Notification\NotifySystem;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;

class QuestionController extends Controller
{
    /**
     * @var \Delorius\Page\Breadcrumb\BreadcrumbBuilder
     * @service breadCrumbs
     * @inject
     */
    public $breadCrumbs;

    /**
     * @var NotifySystem
     * @service notify.system
     * @inject
     */
    public $notify;

    /** @var int */
    protected $perPage;

    /** @var array */
    protected $config = array();

    public function before()
    {
        $this->config = $this->container->getParameters('cms.question');
        $this->perPage = $this->config['page'];
        if ($this->config['layout'])
            $this->layout($this->config['layout']);
    }

    /**
     * @AddTitle Вопрос-ответ #question
     */
    public function addAction()
    {
        $this->breadCrumbs->setLastItem('Написать вопрос');
        $this->response($this->view->load('cms/question/add'));
    }

    /**
     * @Post
     */
    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $question = new Question();
            $question->values($post);
            $question->status = 0;
            $question->save();
            $result['ok'] = _t('CMS:Core', 'Your question added');
            $s = _sf('<h1>{0}</h1><p>Телеон:{1}</p><p>Email:{2}</p><p>Вопрос:{3}</p>', $question->name, $question->phone, $question->email, $question->text);
            $this->notify->send('Написан вопрос на сайте', $s);
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @AddTitle Вопрос-ответ
     */
    public function listAction($page)
    {
        $questions = Question::model()->active()->sort();
        $pagination = PaginationBuilder::factory($questions)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage($this->perPage)
            ->addQueries($this->httpRequest->getQuery())
            ->setRoute('question');
        $this->getHeader()->setPagination($pagination);

        $arrNews = $pagination->result();
        $ids = array();
        $var['questions'] = array();
        foreach ($arrNews as $item) {
            $var['questions'][] = $item;
            $ids[] = $item->pk();
        }

        $var['pagination'] = $pagination;
        $this->response($this->view->load('cms/question/list', $var));

    }

}