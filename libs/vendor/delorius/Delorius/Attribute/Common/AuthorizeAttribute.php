<?php
namespace Delorius\Attribute\Common;

use Delorius\Attribute\Attribute;
use Delorius\Attribute\IAttributeOnStartup;
use Delorius\Exception\Error;
use Delorius\Exception\ForbiddenAccess;
use Delorius\Security\Permission;
use Delorius\Security\User;

/**
 * Class AuthorizeAttribute
 * @package Delorius\Attribute\Common
 * Example : @User(role=user|admin,user=login1,login2,ids=1|2|34|533,isLoggedIn=true,allowed=func1|func2)
 * если isLoggedIn=false, то пользователю быть авторизованым не обезательно
 */
abstract class AuthorizeAttribute extends Attribute implements IAttributeOnStartup
{

    const DEFAULT_IS_LOGIN = true;
    /** @var bool требования авторизации */
    protected $isLoggedIn;
    /** @var array допустимые роли */
    protected $role;
    /**  @var array допустимые пользователи */
    protected $login;
    /** @var  array допустимые индефикаторы пользователей */
    protected $ids;
    /** @var sting пространсто авторизации */
    protected $namespace;
    /** @var string контрол авторизации */
    protected $class_login;//= 'CMS:Core:Authorized:login';

    public function setParams(array $params = null)
    {
        $this->role = explode('|', $params['role']);
        $this->login = explode('|', $params['user']);
        $this->ids = explode('|', $params['ids']);
        $this->isLoggedIn = isset($params['isLoggedIn']) ? $params['isLoggedIn'] : self::DEFAULT_IS_LOGIN;
    }


    function onStartup(\Delorius\Application\UI\Controller $controller)
    {

        if (!$this->class_login) {
            throw new Error('Не указан контролер куда отправлять пользователя без авторизации');
        }

        // установка пространства авторизации
        $this->setNamespace($controller->user);
        $this->setAuthenticator($controller->user);
        $this->setAuthorizator($controller->user);

        // если не требуется авторизация
        if (!$this->isLoggedIn)
            return null;

        if (!$controller->user->isLoggedIn()) {
            $controller->forward($this->class_login);
            $controller->endProgram(true);
        }

        // is_root
        if ($controller->user->isAllowed()) {
            return true;
        }

        if (!$this->isRole($controller->user->getRoles())) {
            throw new ForbiddenAccess('Access to this functionality is prohibited');
        }

        if (!$this->isUser($controller->user->getIdentity()->login)) {
            throw new ForbiddenAccess('Access this login is prohibited');
        }

        if (!$this->isId($controller->user->getId())) {
            throw new ForbiddenAccess('Access this Id is prohibited');
        }

    }

    protected function isRole(array $roles)
    {
        if (empty($this->role[0]))
            return true;
        foreach ($roles as $role) {
            if (is_int(array_search($role, $this->role)))
                return true;
        }
        return false;
    }


    protected function isUser($login)
    {
        if (empty($this->login[0]))
            return true;
        if (is_int(array_search($login, $this->login)))
            return true;
        return false;
    }

    protected function isId($id)
    {
        if (empty($this->ids[0]))
            return true;
        if (is_int(array_search($id, $this->ids)))
            return true;
        return false;
    }

    protected function setNamespace(User $user)
    {
        if ($this->namespace) {
            $user->getStorage()->setNamespace($this->namespace);
        }
    }

    abstract protected function setAuthenticator(User $user);

    abstract protected function setAuthorizator(User $user);


}
