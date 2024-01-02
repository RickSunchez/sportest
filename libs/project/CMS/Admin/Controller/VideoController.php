<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Tags;
use CMS\Core\Entity\Video;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Arrays;
use RicardoFiorani\Exception\ServiceNotAvailableException;
use RicardoFiorani\Matcher\VideoServiceMatcher;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Видео #admin_video
 */
class VideoController extends Controller
{

    /** @AddTitle Список */
    public function listAction($page)
    {
        $videos = Video::model()->sort();
        $var['get'] = $get = $this->httpRequest->getQuery();

        $pagination = PaginationBuilder::factory($videos)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_video');

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['videos'] = array();
        foreach ($result as $item) {
            $var['videos'][] = $item->as_array();
        }
        $this->response($this->view->load('cms/video/list', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать видео
     * @Model(name=CMS\Core\Entity\Video)
     */
    public function editAction(Video $model)
    {
        $var = array();
        $var['video'] = $model->as_array();
        $var['meta'] = $model->getMeta()->as_array();
        $var['image'] = $model->getImage()->as_array();
        $var['tags'] = Arrays::resultAsArray($model->getTags());
        $this->response($this->view->load('cms/video/edit', $var));
    }

    /**
     * @param string $term
     * @throws Error
     */
    public function tagsDataAction($term)
    {
        $tags = Tags::model()
            ->sort()
            ->whereByTargetType(Video::model())
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
    public function addDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $vsm = new VideoServiceMatcher();
            $parser = $vsm->parse($post['url']);
            if (!$parser->isEmbeddable()) {
                throw new Error('Видео не доступно');
            }
            $video = new Video();
            $video->url = $post['url'];
            $video->save();

            $result['ok'] = true;
            $result['id'] = $video->pk();

        } catch (ServiceNotAvailableException $e) {
            $result['error'] = $e->getMessage();
        } catch (OrmValidationError $e) {
            $result['error'] = 'Не удалось сохранить видео';
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
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
            $video = new Video($post['video'][Video::model()->primary_key()]);
            $video->values($post['video']);
            $register = $this->container->getService('register');
            $video->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Видео изменено: id=[id]',
                    $orm
                );
            };
            $video->save(true);

            $meta = $video->getMeta();
            $meta->values($post['meta']);
            $meta->save(true);

            foreach ($post['tags'] as $tag) {
                $video->setTag($tag);
            }

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'id' => $video->pk()
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
        $video = new Video($post['id']);
        try {
            if (!$video->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->container->getService('register');
            $video->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Видео удалено: [name]',
                    $orm
                );
            };
            $video->delete(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
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
        $video = new Video($post['id']);
        if ($video->loaded()) {
            $video->status = (int)$post['status'];
            $video->save(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } else
            $result['error'] = _t('CMS:Admin', 'Object not found');
        $this->response($result);
    }


}