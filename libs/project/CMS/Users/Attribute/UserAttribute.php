<?php
namespace CMS\Users\Attribute;

use CMS\Users\Model\AuthenticatorUser;
use CMS\Users\Model\AuthorizatorUser;
use Delorius\Attribute\Common\AuthorizeAttribute;
use Delorius\Core\Environment;
use Delorius\Security\User;

/**
 * Class UserAttribute
 * Example : @Users(role=user|Users,user=login1|login2,ids=1|2|34|533)
 * @package Delorius\Attribute\Common
 */
class UserAttribute extends AuthorizeAttribute{

    protected $class_login = 'CMS:Users:Authorized:auth';

    public function __construct(){
        $this->namespace = User::DEFAULT_NAMESPACE;
    }

    protected function setAuthenticator(\Delorius\Security\User $user){
        $user->setAuthenticator(new AuthenticatorUser());
    }

    protected function setAuthorizator(\Delorius\Security\User $user){
        $user->setAuthorizator(new AuthorizatorUser(Environment::getContext()));
    }

}
