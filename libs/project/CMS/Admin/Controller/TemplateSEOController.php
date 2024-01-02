<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\SEO\Entity\Template;
use CMS\SEO\Model\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Strings;


/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Шаблоны текстов #admin_tmp?action=list
 */
class TemplateSEOController extends Controller
{

    /**
     * @var  Register
     * @inject
     */
    public $register;

    /** @AddTitle Список */
    public function listAction($page)
    {
        $articles = Template::model()
            ->select('id', 'name', 'count', 'date_cr', 'date_edit')
            ->order_created('desc');
        $var['get'] = $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($articles)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(ADMIN_PER_PAGE)
            ->addQueries($get);

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['templates'] = array();
        foreach ($result as $item) {
            $item['edited'] = $item['date_edit'] ? date('d.m.Y H:i', $item['date_edit']) : date('d.m.Y H:i', $item['date_cr']);
            $var['templates'][] = $item;
        }
        $this->response($this->view->load('cms/seo/template/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить шаблон
     */
    public function addAction()
    {
        $this->response($this->view->load('cms/seo/template/edit'));
    }


    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать шаблон
     * @Model(name=CMS\SEO\Entity\Template)
     */
    public function editAction(Template $model)
    {
        $var = array();
        $var['tmp'] = $model->as_array();
        $var['count'] = count(Helpers::getTemplates($model->pk()));
        $this->response($this->view->load('cms/seo/template/edit', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $orm = new Template($post['tmp']['id']);
            $orm->values($post['tmp']);
            $register = $this->register;
            $orm->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Шаблон изменен: id=[id]',
                    $orm
                );
            };
            $orm->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $orm->pk()
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
        $orm = new Template($post['id']);
        try {
            if (!$orm->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->register;
            $orm->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Шаблон удален: [name]',
                    $orm
                );
            };
            $orm->delete(true);
            $result['ok'] = 'Статья удалена';
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function generateDataAction()
    {
        $post = $this->httpRequest->getPost();
        $orm = new Template($post['tmp']['id']);
        if ($orm->loaded()) {
            $orm->values($post['tmp']);
            $register = $this->register;
            $orm->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Шаблон изменен: id=[id]',
                    $orm
                );
            };
            $orm->save(true);
        }

        Helpers::clean($orm->pk());
        $texts = Helpers::getTemplates($orm->pk());
        $result['count'] = count($texts);
        $result['tmp'] = $orm->as_array();

        $this->response($result);
    }


    /**
     * @Post
     */
    public function exampleDataAction()
    {
        $post = $this->httpRequest->getPost();
        $post['tmp']['text'];
        $result['text'] = Helpers::parserText(Strings::textGenerator($post['tmp']['text']));
        $this->response($result);
    }


}