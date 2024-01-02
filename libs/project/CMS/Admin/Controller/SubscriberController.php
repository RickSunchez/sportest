<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Mail\Entity\Subscriber;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Utils\Strings;
use Delorius\Utils\Validators;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Подписчики #admin_subscriber?action=list
 */
class SubscriberController extends Controller
{

    /**
     * @AddTitle Список
     */
    public function listAction()
    {
        $subs = new Subscriber();
        $subs->order_by('ip', 'DESC')->order_by('date_edit', 'DESC');

        $get = $this->httpRequest->getQuery();
        if ($get['search']) {
            $subs->where('email', 'like', '%' . $get['email'] . '%');
        }

        if ($get['id']) {
            $subs->whereSubscriptionId($get['id']);
        }

        $pagination = PaginationBuilder::factory($subs)
            ->setItemCount(false)
            ->setPage((int)$get['page'])
            ->setItemsPerPage(50)
            ->addQueries($get)
            ->setRoute('admin_subscriber');

        $arr = $pagination->result();
        foreach ($arr as $item) {
            $var['subs'][] = $item->as_array();
        }
        $var['pagination'] = $pagination;
        $var['get'] = $get;

        $this->response($this->view->load('cms/sub/list', $var));

    }


    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        if (isset($post['id'])) {
            $subs = new Subscriber((int)$post['id']);
            if ($subs->loaded()) {
                try {
                    $subs->values($post, array('email', 'name', 'status'));
                    $subs->save(true);
                    $result['ok'] = 'Готово';
                } catch (OrmValidationError $e) {
                    $result['errors'] = $e->getErrorsMessage();
                }
            } else {

                $result['errors'][] = 'Нет такого объекта';
            }
        } elseif (isset($post['email']) && Validators::isEmail($post['email'])) {
            try {
                $subs = new Subscriber();
                if($subs->unique('email',Strings::trim($post['email']))){
                    $subs->email = $post['email'];
                    $subs->status = 1;
                    $subs->save(true);
                }else{
                    $result['errors'][] = 'Такой email уже в базе';
                }

            } catch (OrmValidationError $e) {
                $result['errors'] = $e->getErrorsMessage();
            }
        }else{
            $result['errors'][] = 'Нет такого объекта';
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function deleteDataAction()
    {
        $post = $this->httpRequest->getPost();
        $subs = new Subscriber($post['id']);
        try {
            if (!$subs->loaded())
                throw new Error(_t('CMS:Admin', 'Object not found'));

            $register = $this->container->getService('register');
            $subs->onAfterDelete[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Email удален из подписчиков: [email]',
                    $orm
                );
            };
            $subs->delete(true);
            $result['ok'] = _t('CMS:Admin', 'These modified');
        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }
        $this->response($result);
    }


}