<?php
namespace CMS\Admin\Controller;

use CMS\Mail\Entity\Delivery;
use CMS\Mail\Entity\Subscriber;
use CMS\Mail\Entity\Subscription;
use CMS\Mail\Model\SubscriberBuilder;
use Delorius\Application\UI\Controller;
use Delorius\Exception\Error;
use Delorius\Exception\NotFound;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Validators;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Список рассылок #admin_delivery?action=list
 */
class DeliveryController extends Controller
{
    /**
     * @service notify
     * @inject
     */
    public $notify;

    /**
     * @AddTitle Отправка письма
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     */
    public function mailAction()
    {
        $this->response($this->view->load("cms/delivery/send"));
    }

    /**
     * @Post
     */
    public function sendMailDataAction()
    {
        $post = $this->httpRequest->getPost();
        try {
            if (!Validators::isEmail($post['email'])) {
                throw new Error(_t('CMS:Admin', 'Specify email'));
            }
            $this->notify->setAddressee($post['email']);

            if (!$this->notify->send($post['subject'], $post['message'])) {
                throw new Error(_t('CMS:Admin', 'Failed to send email'));
            }
            $result['ok'] = _t('CMS:Admin', 'E-mail has been sent');

        } catch (Error $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);

    }

    /** @AddTitle Рассылки */
    public function listAction()
    {
        $delivery = new Delivery();
        $result = $delivery->order_by('finished', 'DESC')->order_pk()->find_all();
        $var['delivery'] = $result;
        $this->response($this->view->load("cms/delivery/list", $var));
    }

    /**
     * @AddTitle Добавить письмо
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     */
    public function addAction()
    {
        $subs = Subscription::model()->whereType(Subscription::TYPE_SUB)->order_pk('desc')->find_all();
        foreach ($subs as $sub) {
            $var['groups'][] = array(
                'id' => $sub->pk(),
                'name' => $sub->name
            );
        }
        $var['group_id'] = $this->httpRequest->getQuery('group_id');
        $this->response($this->view->load("cms/delivery/add", $var));
    }

    /**
     * @AddTitle Редактировать письмо
     * @JsRemote(/source/manager/ckeditor/ckeditor.js,/source/manager/ckeditor/admin_config.js)
     */
    public function editAction()
    {
        $id = $this->httpRequest->getQuery('id');
        $delivery = new Delivery((int)$id);
        if (!$delivery->loaded()) {
            throw new NotFound('Ссылка не найдена');
        }
        $subs = Subscription::model()->whereType(Subscription::TYPE_SUB)->order_pk('desc')->find_all();
        foreach ($subs as $sub) {
            $var['groups'][] = array(
                'id' => $sub->pk(),
                'name' => $sub->name
            );
        }
        $var['group_id'] = $delivery->group_id;
        $var['delivery'] = $delivery->as_array();
        $this->response($this->view->load("cms/delivery/add", $var));
    }

    /**
     * @Post
     */
    public function deleteAction()
    {
        $id = $this->httpRequest->getQuery('id');
        $delivery = new Delivery((int)$id);
        if (!$delivery->loaded()) {
            throw new NotFound('Not found Delivery by id = ' . $id);
        }
        $delivery->delete(true);
        $this->httpResponse->redirect(link_to('admin_delivery', array('action' => 'list')));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        try {
            if ((int)$post['delivery_id'] == 0) {
                $delivery = new Delivery();
                $delivery->values($post, array('subject', 'message', 'status', 'group_id'));
                $delivery->startOver();
            } else {
                $delivery = new Delivery((int)$post['delivery_id']);
                $delivery->values($post, array('subject', 'message', 'status', 'group_id'));
            }
            $delivery->save(true);
            $result['ok'] = 'Готово';
            $result['id'] = $delivery->pk();
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);
    }

    /**
     * @Post
     */
    public function testDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('error' => '', 'ok' => '');

        $res = false;
        $subscriber = SubscriberBuilder::factory($post)->getOwner();
        if ($subscriber->loaded()) {
            $res = $subscriber->sendMessage($post['delivery']['subject'], $post['delivery']['message'], $post['delivery']['group_id']);
        }
        if ($res)
            $result['ok'] = 'Письмо отправлено';
        else
            $result['error'] = 'Не удалось отправить письмо';
        $this->response($result);
    }

    /**
     * @Post
     */
    public function resetDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('error' => '', 'ok' => '');
        $delivery = new Delivery((int)$post['delivery_id']);
        if ($delivery->loaded()) {
            try {
                $delivery->startOver();
                $delivery->save(true);
                $result['ok'] = 'Готово';
                $result['id'] = $delivery->pk();
            } catch (OrmValidationError $e) {
                $result['error'] = $e->getErrorsMessage();
            }
        } else {
            $result['error'] = 'Нет такого объекта';
        }
        $this->response($result);
    }


    /*
     * Delivery GROUP
     */

    /**
     * @AddTitle Сообщение для подписчивок
     * @JsRemote(/source/manager/ckeditor/ckeditor.js)
     */
    public function sendAction()
    {
        $subs = Subscription::model()->order_by('group_id', 'DESC')->find_all();
        foreach ($subs as $sub) {
            $var['groups'][] = array(
                'id' => $sub->pk(),
                'name' => $sub->name
            );
        }
        $var['id'] = $this->httpRequest->getQuery('id');
        $this->response($this->view->load('mail/index', $var));
    }


    public function countMailGroupDataAction()
    {
        $post = $this->httpRequest->getPost();
        $count = Subscriber::model()
            ->whereSubscriptionId((int)$post['id'])
            ->find_all()
            ->count();
        $this->response(array('count' => $count));
    }

    public function sendMailsDataAction()
    {
        $result['mail'] = array();
        $post = $this->httpRequest->getPost();
        $arMails = Subscriber::model()->whereSubscriptionId((int)$post['groupId'])->limit($post['limit'])->offset($post['offset'])->find_all();
        $subject = $post['mail']['subject'];
        $message = $post['mail']['text'];
        $groupId = $post['groupId'];
        foreach ($arMails as $sender) {
            $result['mail'][] = $sender->email;
            $sender->sendMessage($subject, $message, $groupId);
            sleep(1);
        }
        $this->response($result);
    }


}