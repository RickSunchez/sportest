<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Article;
use CMS\Core\Entity\Tags;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use CMS\Catalog\Entity\Category;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Статьи #admin_article
 */
class ArticleController extends Controller
{

    protected $tmp = 'cms/article';

    /**
     * @var  Register
     * @inject
     */
    public $register;

    /** @AddTitle Список */
    public function listAction($page)
    {
        $articles = Article::model()->order_created('desc');
        $var['get'] = $get = $this->httpRequest->getQuery();

        if (isset($get['cid'])) {
            $cids = array($get['cid']);
            $categories = Category::model($get['cid'])->getChildren();
            if (!empty($categories)) {
                foreach ($categories as $cat) {
                    $cids[] = $cat->cid;
                }
            }
            $articles
                ->where('cid', 'in', $cids);
        }

        if (Helpers::getDomains() && $get['domain']) {
            $articles->where('site', '=', $get['domain']);
        } else {
            $articles->where('site', '=', 'www');
        }

        $pagination = PaginationBuilder::factory($articles)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_article');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['articles'] = array();
        foreach ($result as $item) {
            $var['articles'][] = $item->as_array();
        }
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load($this->tmp .= '/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить статью
     */
    public function addAction($cid)
    {
        $var['cid'] = $cid;
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load($this->tmp .= '/edit', $var));
    }


    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать статью
     * @Model(name=CMS\Core\Entity\Article)
     */
    public function editAction(Article $model)
    {
        $var = array();
        $var['article'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();
        $var['tags'] = Arrays::resultAsArray($model->getTags());
        $var['domain'] = Helpers::getDomains();
        $var['multi'] = Helpers::isMultiDomain();
        $this->response($this->view->load($this->tmp .= '/edit', $var));
    }

    public function tagsDataAction($term)
    {
        $tags = Tags::model()
            ->sort()
            ->whereByTargetType(Article::model())
            ->where('name', 'like', '%' . $term . '%')
            ->find_all();
        $result = Arrays::each($tags, function ($value) {
            return $value->name;
        });
        if (!count($result)) {
            die('{}');
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();

        try {
            $article = new Article($post['article']['id']);
            $article->values($post['article']);
            $register = $this->register;
            $article->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Статья изменена: id=[id]',
                    $orm
                );
            };
            $article->save(true);

            if (count($post['meta'])) {
                $meta = $article->getMeta();
                $meta->values($post['meta']);
                $meta->save(true);
            }

            if (count($post['tags'])) {
                foreach ($post['tags'] as $tag) {
                    $article->setTag($tag);
                }
            }


            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $article->pk()
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
        $article = new Article($post['id']);
        try {
            if (!$article->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->register;
            $article->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Статья удалена: [name]',
                    $orm
                );
            };
            $article->delete(true);
            $result['ok'] = 'Статья удалена';
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
        $article = new Article($post['id']);
        if ($article->loaded()) {
            $article->status = (int)$post['status'];

            $register = $this->register;
            $article->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Изменен статус статьи на  status=[status]',
                    $orm
                );
            };

            $article->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }

}