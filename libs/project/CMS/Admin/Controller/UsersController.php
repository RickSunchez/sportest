<?php
namespace CMS\Admin\Controller;

use CMS\Core\Component\Register;
use CMS\Mail\Model\Notification\Notify;
use CMS\Mail\Model\Notification\NotifySender;
use CMS\Mail\Model\Notification\NotifySystem;
use CMS\Users\Entity\AttrName;
use CMS\Users\Entity\GroupAttr;
use CMS\Users\Entity\User;
use CMS\Users\Entity\UserAttr;
use CMS\Users\Model\AuthenticatorUserId;
use Delorius\Application\UI\Controller;
use Delorius\Core\Common;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\Security\AuthenticationException;
use Delorius\Utils\Arrays;
use Delorius\Utils\Strings;
use Shop\Store\Entity\Balance;
use Shop\Store\Entity\Cashflow;
use Shop\Store\Exception\BalanceError;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Пользователи #admin_user?action=list
 */
class UsersController extends Controller
{

    /**
     * @var \CMS\Core\Component\Register
     * @service register
     * @inject
     */
    public $register;

    /**
     * @service notify
     * @inject
     */
    public $notify;

    /**
     * @AddTitle Список
     */
    public function listAction($page)
    {

        $config = $this->container->getParameters('cms.admin');

        $users = User::model()->order_by('date_last_login', 'desc')->order_by('date_edit', 'desc');
        $get = $this->httpRequest->getQuery();

        if (isset($get['id'])) {
            $users->where('user_id', '=', $get['id']);
        }

        if (isset($get['email'])) {
            $users->where('email', 'like', $get['email']);
        }

        if (isset($get['ip'])) {
            $users->where('ip', 'like', $get['ip']);
        }

        if (isset($get['active'])) {
            $users->where('active', '=', $get['active']);
        }

        if (isset($get['role'])) {
            $users->where('role', 'like', $get['role']);
        }

        $pagination = PaginationBuilder::factory($users)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(20)
            ->addQueries($get)
            ->setRoute('admin_user');

        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['users'] = Arrays::resultAsArray($pagination->result());
        $var['attr_name'] = array();
        $var['user_attrs'] = array();

        $userIds = array();
        foreach ($var['users'] as $user) {
            $userIds[] = $user['user_id'];
        }

        if (count($config['user']['attrs'])) {
            $attrIds = array();
            $attr_name = AttrName::model()->where('code', 'IN', $config['user']['attrs'])->sort()->find_all();
            foreach ($attr_name as $attr) {
                $attrIds[] = $attr->pk();
                $var['attr_name'][] = $attr->as_array();
            }

            if (sizeof($userIds) && sizeof($attrIds)) {
                $user_attrs = UserAttr::model()->where('user_id', 'IN', $userIds)->where('attr_id', 'IN', $attrIds)->find_all();
                foreach ($user_attrs as $v) {
                    $var['user_attrs'][$v->user_id][$v->attr_id] = $v->as_array();
                }
            }
        }

        if (defined('SHOP_STORE') && sizeof($userIds)) {
            $var['balance_isset'] = true;
            $balance = Balance::model()->where('user_id', 'in', $userIds)->find_all();
            $var['balance'] = Arrays::resultAsArrayKey($balance, 'user_id', true);
        }

        $this->response($this->view->load('cms/user/list', $var));
    }

    /**
     * @AddTitle Добавить
     */
    public function addAction()
    {
        $var['attr_name'] = Arrays::resultAsArray(AttrName::model()->find_all());
        $var['groups'] = Arrays::resultAsArray(GroupAttr::model()->find_all());
        $this->response($this->view->load('cms/user/add', $var));
    }

    /**
     * @AddTitle Редактировать
     * @Model(name=CMS\Users\Entity\User)
     */
    public function editAction(User $model)
    {
        $var['user'] = $model->as_array();
        $var['groups'] = Arrays::resultAsArray(GroupAttr::model()->find_all());
        $var['attr_name'] = Arrays::resultAsArrayKey(AttrName::model()->find_all(), AttrName::model()->primary_key(), true);
        $var['attr_value'] = Arrays::resultAsArrayKey(UserAttr::model()->where('user_id', '=', $model->pk())->find_all(), 'attr_id', true);
        $this->response($this->view->load('cms/user/add', $var));
    }

    /**
     * @Post
     */
    public function saveDataAction()
    {
        $post = $this->httpRequest->getPost();
        $result = array('errors' => '', 'ok' => '');
        try {

            $user = new User((int)$post['user']['user_id']);
            if (!empty($post['user']['password1']) && $post['user']['password1'] == $post['user']['password2']) {
                $user->password = $post['user']['password1'];
            }
            unset($post['user']['password']);
            $user->values($post['user']);

            $register = $this->register;
            $user->onAfterSave[] = function ($orm) use ($register) {
                $register->add(
                    Register::TYPE_ATTENTION,
                    Register::SPACE_ADMIN,
                    'Пользователь изменён',
                    $orm
                );
            };
            $user->save(true);

            foreach ($post['attr'] as $v) {
                foreach ($v as $value) {
                    $user->addAttr($value);
                }
            }
            $result['ok'] = _t('CMS:Admin', 'These modified');
            $result['id'] = $user->pk();
        } catch (OrmValidationError $e) {
            $result['errors'] = $e->getErrorsMessage();
        }
        $this->response($result);

    }

    /**
     * @Post
     * @Model(name=CMS\Users\Entity\User)
     */
    public function bannedDataAction(User $model)
    {
        $model->active = 0;
        $register = $this->register;
        $model->onAfterSave[] = function ($orm) use ($register) {
            $register->add(
                Register::TYPE_ATTENTION,
                Register::SPACE_ADMIN,
                'Пользователь забаннен',
                $orm
            );
        };
        $model->save(true);
        $this->response(array('ok'));
    }

    /**
     * @Post
     * @Model(name=CMS\Users\Entity\User)
     */
    public function unbannedDataAction(User $model)
    {
        $model->active = 1;
        $register = $this->register;
        $model->onAfterSave[] = function ($orm) use ($register) {
            $register->add(
                Register::TYPE_ATTENTION,
                Register::SPACE_ADMIN,
                'Пользователь разбаннен',
                $orm
            );
        };
        $model->save(true);
        $this->response(array('ok'));
    }

    /**
     * @Post
     * @User(isLoggedIn=false)
     */
    public function authDataAction()
    {
        $id = $this->httpRequest->getPost('id');
        try {
            $this->user->setAuthenticator(new AuthenticatorUserId());
            $register = $this->register;
            $this->user->onLoggedIn[] = function ($sender) use ($register, $id) {
                $register->add(
                    Register::TYPE_INFO,
                    Register::SPACE_ADMIN,
                    'Авторизовался под пользователем: id:' . $id
                );
            };
            $this->user->login($id);
            $result['ok'] = 'Вы авторизованы под пользователем';
        } catch (AuthenticationException $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);
    }

    /**
     * @Post
     * @Model(name=CMS\Users\Entity\User)
     */
    public function remindDataAction(User $model)
    {

        try {
            $new_password = Strings::random(6);
            $model->password = $new_password;
            $model->hash = '';
            $model->save();
            $this->notify->setAddressee($model->email, $model->login);
            $this->notify->send(
                _t('CMS:Users', 'Recover password'),
                $this->view->load('cms/mail/remind', array(
                    'user' => $model,
                    'new_password' => $new_password
                ))
            );
            $this->register->add(
                Register::TYPE_ATTENTION,
                Register::SPACE_ADMIN,
                'Выслан новый пароль пользователю',
                $model
            );
            $this->response(array('ok' => _t('CMS:Users', 'The password is changed')));
        } catch (Error $e) {
            $var['error'] = $e->getMessage();
        }

    }


    /**
     * @AddTitle Баланс
     * @Model(name=CMS\Users\Entity\User)
     */
    public function balanceAction(User $model, $page)
    {
        $balance = Balance::getByUserId($model->pk());
        $cashflow = Cashflow::model()->where('user_id', '=', $model->pk())->sort();
        $get = $this->httpRequest->getQuery();
        $pagination = PaginationBuilder::factory($cashflow)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(50)
            ->addQueries($get)
            ->addQueries(array('action' => 'balance'))
            ->setRoute('admin_user');

        $var['pagination'] = $pagination;
        $var['get'] = $get;
        $var['cashflow'] = Arrays::resultAsArray($pagination->result());
        $var['balance'] = $balance->as_array();
        $var['user'] = $model->as_array();
        $this->response($this->view->load('cms/user/balance', $var));
    }


    /**
     * @Post
     * @Model(name=CMS\Users\Entity\User,field=user_id)
     */
    public function balanceDataAction(User $model)
    {
        $post = $this->httpRequest->getPost();
        $balance = Balance::getByUserId($model->pk());
        try {
            if (Cashflow::MINUS == $post['type']) {
                $balance->withdraw($post['value'], $post['reason']);
            } elseif (Cashflow::PLUS == $post['type']) {
                $balance->addfunds($post['value'], $post['reason']);
            } else {
                throw new BalanceError(_t('CMS:Users', 'Unknown type of operation'));
            }
            $result['ok'] = _t('CMS:Users', 'Operation completed');
        } catch (BalanceError $e) {
            $result['error'] = $e->getMessage();
        }

        $this->response($result);

    }


}