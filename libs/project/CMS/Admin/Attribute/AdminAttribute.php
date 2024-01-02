<?php
namespace CMS\Admin\Attribute;

use CMS\Admin\Model\AuthenticatorAdmin;
use CMS\Admin\Model\AuthorizatorAdmin;
use Delorius\Attribute\Common\AuthorizeAttribute;
use Delorius\Core\Environment;

/**
 * Class AdminAttribute
 * Example : @Admin(role=user|admin,user=login1|login2,ids=1|2|34|533)
 * @package Delorius\Attribute\Common
 */
class AdminAttribute extends AuthorizeAttribute{

    protected $namespace = 'admin';
    protected $class_login = 'CMS:Admin:Authorized:login';

    protected function setAuthenticator(\Delorius\Security\User $user){
        $user->setAuthenticator(new AuthenticatorAdmin());
    }

    protected function setAuthorizator(\Delorius\Security\User $user){
        $user->setAuthorizator(new AuthorizatorAdmin(Environment::getContext()));
    }

}
