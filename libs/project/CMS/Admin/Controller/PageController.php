<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Core\Entity\Image;
use CMS\Core\Entity\Page;
use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;
use Delorius\Utils\Finder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Страницы сайта #admin_page
 */
class PageController extends Controller
{
    
    protected $tmp = 'cms/page';
    
    /**
     * @var  Register
     * @inject
     */
    public $register;

    /** @AddTitle Список страниц */
    public function listAction()
    {
        $var['pages'] = $this->getPages();
        $ids = array();
        foreach ($var['pages'] as $page) {
            $ids[] = $page['id'];
        }
        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Page::model());
            $var['images'] = Arrays::resultAsArray($images->find_all());
        }
        $var['multi'] = Helpers::isMultiDomain();
        $var['domain'] = Helpers::getDomains();
        $this->response($this->view->load($this->tmp .= '/list', $var));
    }

    /**
     * @Model(name=CMS\Core\Entity\Page)
     */
    public function redirectAction(Page $model){
        $this->httpResponse->redirect($model->link());
    }

    public function pagesDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result['pages'] = $this->getPages($post['site']);
        $ids = array();
        foreach ($result['pages'] as $page) {
            $ids[] = $page['id'];
        }
        if (sizeof($ids)) {
            $images = Image::model()
                ->whereByTargetId($ids)
                ->whereByTargetType(Page::model());
            $result['images'] = Arrays::resultAsArray($images->find_all());
        }
        $this->response($result);
    }

    public function deleteAction()
    {
        $page_id = (int)$this->httpRequest->getPost('id');
        $result = array('error' => '', 'ok' => '');

        try {
            $page = new Page($page_id);
            $register = $this->register;
            $page->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Страница удалена : [short_title]',
                    $orm
                );
            };
            if (!$page->loaded()) {
                throw new Error('Нет такой страницы');
            }
            if ($page->hasChildren()) {
                throw new Error('Ошибка: есть дочернии страницы');
            }
            $page->delete(true);
            $result['ok'] = 'Готово';


        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Добавить страницу
     */
    public function addAction($domain)
    {
        $var['pid'] = $id = (int)$this->httpRequest->getQuery('id');
        if ($id) {
            $page = new Page($id);
            if ($page->loaded())
                $var['domain'] = $domain = $page->site;
        } else {
            $var['domain'] = $domain;
        }
        $var['tpl'] = $this->getTemplate();
        $var['domains'] = Helpers::getDomains();
        $config = $this->container->getParameters('site.templates.' . $domain);
        $var['default'] = array(
            'template' => $config['template'] ? $config['template'] : $this->container->getParameters('site.template'),
            'layout' => $config['layout'] ? $config['layout'] : $this->container->getParameters('site.layout'),
            'mobile' => $config['mobile'] ? $config['mobile'] : $this->container->getParameters('site.mobile'),
        );
        $this->response($this->view->load($this->tmp .= '/add', $var));
    }

    /**
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     * @AddTitle Редактировать страницу
     * @Model(name=CMS\Core\Entity\Page)
     */
    public function editAction(Page $model)
    {
        $var['page'] = $model->as_array();
        $var['pid'] = $model->pid;
        $var['tpl'] = $this->getTemplate();
        $var['domains'] = Helpers::getDomains();
        $var['image'] = $model->getImage()->as_array();
        $options = $model->getOptions();
        foreach ($options as $opt) {
            $var['options'][$opt->code][$opt->name] = $opt->value;
        }
        $this->response($this->view->load($this->tmp .= '/add', $var));
    }

    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');

        try {
            $page = new Page($post['page'][Page::model()->primary_key()]);
            $page->values($post['page']);
            $register = $this->register;
            $page->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Изменены данные страницы: page=[id]',
                    $orm
                );
            };
            $page->save(true);

            if (isset($post['options']) && count($post['options'])) {
                $options = $post['options'];
                $merge = array();
                foreach ($options as $code => $opts) {
                    if (count($opts)) {
                        foreach ($opts as $name => $value) {
                            $merge[] = array(
                                'code' => $code,
                                'name' => $name,
                                'value' => $value
                            );
                        }
                    }
                }

                $page->mergeOptions($merge);
            }

            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['page'] = $page->as_array();
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    public function changePosDataAction()
    {
        $post = $this->httpRequest->getPost();
        $page = new Page((int)$post['id']);
        if ($page->loaded()) {
            try {
                if ($post['type'] == 'edit') {
                    $page->pos = (int)$post['pos'];
                } else if ($post['type'] == 'up') {
                    $page->pos = $page->pos + 1;
                } else if ($post['type'] == 'down') {
                    $page->pos = $page->pos - 1;
                }
                $page->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
                $result['pages'] = $this->getPages($page->site);
            } catch (OrmValidationError $e) {
                $result['error'] = $e->getErrorsMessage();
            }
        }

        $this->response($result);
    }

    public function mainDataAction()
    {
        $post = $this->httpRequest->getPost();
        $page = new Page($post['id']);
        if ($page->loaded()) {
            try {
                $page->setMain($post['main']);

                $register = $this->register;
                $page->onAfterSave[] = function ($orm) use ($register) {
                    $register->add(
                        Register::TYPE_INFO,
                        Register::SPACE_ADMIN,
                        'Изменен статус страницы быть главное на main=[main]',
                        $orm
                    );
                };

                $page->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
                $result['pages'] = $this->getPages($page->site);
            } catch (OrmValidationError $e) {
                $result['error'] = $e->getErrorsMessage();
            }
        } else
            $result['error'] = 'Страница не найдена';
        $this->response($result);
    }

    public function statusDataAction()
    {
        $post = $this->httpRequest->getPost();
        $page = new Page($post['id']);
        if ($page->loaded()) {
            try {
                $page->status = (int)$post['status'];

                $register = $this->register;
                $page->onAfterSave[] = function ($orm) use ($register) {
                    $register->add(
                        Register::TYPE_INFO,
                        Register::SPACE_ADMIN,
                        'Изменен статус страницы на status=[status]',
                        $orm
                    );
                };

                $page->save(true);
                $result['ok'] = _t('CMS:Admin', 'These modified');
                $result['pages'] = $this->getPages($page->site);
            } catch (OrmValidationError $e) {
                $result['error'] = $e->getErrorsMessage();
            }
        } else
            $result['error'] = 'Страница не найдена';
        $this->response($result);
    }

    private function getTemplate()
    {
        $result = array();
        $dir = $this->container->getParameters('path.template') . '/';
        foreach (Finder::findDirectories('*')->in($dir) as $path) {
            $result['dir'][] = array('name' => $path->getBasename());
            foreach (Finder::findFiles('*.php')->in($dir . $path->getBasename()) as $pathFile) {
                $result['page'][$path->getBasename()][] = array(
                    'name' => str_replace('.php', '', $pathFile->getBasename())
                );
            }
        }
        return $result;
    }

    /** @return array */
    private function getPages($site = 'www')
    {
        $list = Page::model()
            ->where('site', '=', $site)
            ->sort()
            ->select('id','pid','short_title','children','main','pos','status')
            ->cached()
            ->find_all();
        return Arrays::resultAsArray($list,false);
    }

}