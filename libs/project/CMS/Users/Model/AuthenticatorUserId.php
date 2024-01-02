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

class AuthenticatorUserId extends Object implements IAuthenticator
{

    /**
     * Performas an authentication
     * @param array
     * @return \Delorius\Security\Identity
     */
    public function authenticate(array $credentials)
    {
        list($id) = $credentials;

        /** @var \CMS\Users\Entity\User $user */
        $user = new User($id);

        if (!$user->loaded()) {
            throw new AuthenticationException(_t('CMS:Users', 'Invalid username or password'), self::IDENTITY_NOT_FOUND);
        }

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