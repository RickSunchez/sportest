<?php
namespace CMS\Admin\Model;


use CMS\Admin\Entity\Admin;
use Delorius\Core\Object;
use Delorius\Security\AuthenticationException;
use Delorius\Security\IAuthenticator;
use Delorius\Security\Identity;

class AuthenticatorAdmin extends Object implements IAuthenticator
{

    /**
     * Performas an authentication
     * @param array
     * @return \Delorius\Security\Identity
     */
    public function authenticate(array $credentials)
    {
        list($login, $password) = $credentials;

        /** @var \CMS\Admin\Entity\Admin  $user */
        $user = Admin::model()->where('login','=',$login)->find();

        if (! $user->loaded() ) {
            throw new AuthenticationException('Неверный логин или пароль.', self::IDENTITY_NOT_FOUND);
        }

        if(!$user->active){
            throw new AuthenticationException('Пользователь деактивирован.', self::NOT_APPROVED);
        }

        if ($user->hashPassword( $password )!= $user->password) {
            throw new AuthenticationException('Неверный логин или пароль', self::INVALID_CREDENTIAL);
        }

        return new Identity($user->pk(),$user->role ,$user->as_array());
    }

}