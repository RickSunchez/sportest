<?php
namespace CMS\Users\Model;

use CMS\Users\Entity\ACL;
use CMS\Users\Entity\Role;
use Delorius\Security\IAuthorizator;
use Delorius\Security\Permission;
use Delorius\Security\User;
use Delorius\Utils\Arrays;

class AuthorizatorUser extends Permission implements IAuthorizator
{
    /** @var  \Delorius\DI\Container */
    private $conteiner;

    /** @var string */
    protected $type = User::DEFAULT_NAMESPACE;

    public function __construct(\Delorius\DI\Container $conteiner)
    {
        $this->conteiner = $conteiner;
        $roles = $this->_getArrRoles();
        foreach ($roles as $role) {
            if ($role['pid'] == 0) {
                $this->addRole($role['code']);
                if ($role['is_root']) {
                    $this->allow($role['code'], Permission::ALL, Permission::ALL);
                } else {
                    $this->deny($role['code'], Permission::ALL);
                }
                $this->addSubRoles($role['role_id'], $role['code']);
            }
        }

        $acl = $this->_getArrAcl();
        foreach ($acl as $item) {

            if (!$this->hasResource($item['resource']))
                $this->addResource($item['resource']);

            if ($item['status'] == 1) {
                $this->allow($item['target_id'], $item['resource'], $item['privilege']);
            } else {
                $this->deny($item['target_id'], $item['resource'], $item['privilege']);
            }
        }
    }

    public function isAllowed($role = self::ALL, $resource = self::ALL, $privilege = self::ALL)
    {
        if (is_string($resource) && !$this->hasResource($resource)) {
            if (parent::isAllowed($role)) {
                return true;
            } else {
                return false;
            }
        }
        return parent::isAllowed($role, $resource, $privilege);
    }

    /**
     * @param $pid
     */
    protected function addSubRoles($pid, $code)
    {
        $roles = $this->_getArrRoles();
        foreach ($roles as $role) {
            if ($role['pid'] == $pid) {
                $this->addRole($role['code'], $code);
                if ($role['is_root']) {
                    $this->allow($role['code'], Permission::ALL, Permission::ALL);
                } else {
                    $this->deny($role['code'], Permission::ALL);
                }
                $this->addSubRoles($role['role_id'], $role['code']);
            }
        }
    }

    /**
     * @var array
     */
    private $_roles = array();

    /**
     * @return array
     */
    private function _getArrRoles()
    {
        if (!sizeof($this->_roles)) {
            $roles = Role::model()
                ->select()
                ->cached('+1 days')
                ->where('type', '=', $this->type)
                ->order_by('pid')
                ->find_all();
            $this->_roles = $roles;
        }
        return $this->_roles;
    }

    /**
     * @return array
     */
    private function _getArrAcl()
    {
        $acl = ACL::model()
            ->select()
            ->cached('+1 days')
            ->where('target_type', '=', 'role')
            ->where('type', '=', $this->type)
            ->find_all();
        return $acl;
    }


}