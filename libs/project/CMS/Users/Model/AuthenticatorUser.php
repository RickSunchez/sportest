<?php
namespace CMS\Users\Model;

use CMS\Users\Entity\AttrName;
use CMS\Users\Entity\User;
use CMS\Users\Entity\UserAttr;
use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Security\AuthenticationException;
use Delorius\Security\IAuthenticator;
use Delorius\Security\Identity;

class AuthenticatorUser extends Object implements IAuthenticator
{

    /**
     * Performas an authentication
     * @param array
     * @return \Delorius\Security\Identity
     */
    public function authenticate(array $credentials)
    {
        list($email, $password) = $credentials;

        /** @var \CMS\Users\Entity\User $user */
        $user = User::model()->where('email', '=', $email)->find();

        if (!$user->loaded()) {
            throw new AuthenticationException(_t('CMS:Users', 'Invalid username or password'), self::IDENTITY_NOT_FOUND);
        }

        if (!$user->active) {
            throw new AuthenticationException(_t('CMS:Users', 'This user has been deactivated'), self::NOT_APPROVED);
        }

        if ($user->hashPassword($password) != $user->password) {
            throw new AuthenticationException(_t('CMS:Users', 'Invalid username or password'), self::INVALID_CREDENTIAL);
        }
        $user->date_last_login = time();
        $user->save();

        $arr = $user->as_array();

        $attrs = Environment::getContext()->getParameters('cms.user.auth.attrs');
        if (count($attrs)) {
            $result = $user->getAttrs($attrs);
            foreach ($result as $attr) {
                if (!isset($arr[$attr['code']])) {
                    $arr[$attr['code']] = $attr['value'];
                }
            }
        }

        return new Identity($user->pk(), explode(',', $user->role), $arr);
    }

}