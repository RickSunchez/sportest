<?php

namespace CMS\Admin\Controller;

use CMS\SEO\Entity\Redirect;
use Delorius\Application\UI\Controller;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Arrays;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Редиректы #admin_redirect?action=list
 */
class RedirectController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction()
    {
        $redirects = Redirect::model()->sort();
        $var['redirects'] = array();
        foreach ($redirects->find_all() as $item) {
            $var['redirects'][] = $item->as_array();
        }
        $var['moves'] = Arrays::dataKeyValue(Redirect::getMoves());
        $var['paths'] = Arrays::dataKeyValue(Redirect::getPaths());
        $this->response($this->view->load('cms/redirect/list', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            $redirect = new Redirect($post[Redirect::model()->primary_key()]);
            $redirect->values($post);
            $redirect->save(true);

            $result = array(
                'ok' => _t('CMS:Admin', 'These modified'),
                'redirect' => $redirect->as_array()
            );
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\SEO\Entity\Redirect)
     */
    public function deleteDataAction(Redirect $model)
    {
        $model->delete(true);
        $this->response(array('ok'));
    }
}