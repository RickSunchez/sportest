<?php
namespace Delorius\Security\Bridges;

use Delorius\DI\CompilerExtension;

class SecurityExtension extends CompilerExtension
{
    public $defaults = array(
        'debugger' => true,
        'users' => array(), // of [user => password] or [user => ['password' => password, 'roles' => [role]]]
        'roles' => array(), // of [role => parents]
        'resources' => array(), // of [resource => parents]
    );

    /** @var bool */
    private $debugMode;


    public function __construct($debugMode = FALSE)
    {
        $this->debugMode = $debugMode;
    }


    public function loadConfiguration()
    {
        $config = $this->validateConfig($this->defaults);
        $container = $this->getContainerBuilder();

        $container->addDefinition($this->prefix('userStorage'))
            ->setClass('Delorius\Security\IUserStorage')
            ->setFactory('Delorius\Http\Storage\UserStorage');

        $user = $container->addDefinition($this->prefix('user'))
            ->setClass('Delorius\Security\User');

        if ($config['users']) {
            $usersList = $usersRoles = array();
            foreach ($config['users'] as $username => $data) {
                $data = is_array($data) ? $data : array('password' => $data);
                $this->validateConfig(array('password' => NULL, 'roles' => NULL), $data, $this->prefix("security.users.$username"));
                $usersList[$username] = $data['password'];
                $usersRoles[$username] = isset($data['roles']) ? $data['roles'] : NULL;
            }

            $container->addDefinition($this->prefix('authenticator'))
                ->setClass('Delorius\Security\IAuthenticator')
                ->setFactory('Delorius\Security\SimpleAuthenticator', array($usersList, $usersRoles));

            if ($this->name === 'security') {
                $container->addAlias('authenticator', $this->prefix('authenticator'));
            }
        }

        if ($config['roles'] || $config['resources']) {
            $authorizator = $container->addDefinition($this->prefix('authorizator'))
                ->setClass('Delorius\Security\IAuthorizator')
                ->setFactory('Delorius\Security\Permission');

            foreach ($config['roles'] as $role => $parents) {
                $authorizator->addSetup('addRole', array($role, $parents));
            }
            foreach ($config['resources'] as $resource => $parents) {
                $authorizator->addSetup('addResource', array($resource, $parents));
            }

            if ($this->name === 'security') {
                $container->addAlias('authorizator', $this->prefix('authorizator'));
            }
        }

        if ($this->name === 'security') {
            $container->addAlias('user', $this->prefix('user'));
            $container->addAlias('userStorage', $this->prefix('userStorage'));
        }

    }

    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);

        $service = $this->prefix('user');
        $initialize->addBody('$user = $this->getService(?);', array($service));
        $initialize->addBody('
            # isCreate
            \Delorius\Core\ORM::extensionMethod("isCreate",function($_orm) use($user){
                return $user->isAllowed($_orm->table_name(),"create");
            });
            # isRead
            \Delorius\Core\ORM::extensionMethod("isRead",function($_orm) use($user){
                return $user->isAllowed($_orm->table_name(),"read");
            });
            # isUpdate
            \Delorius\Core\ORM::extensionMethod("isUpdate",function($_orm) use($user){
                return $user->isAllowed($_orm->table_name(),"update");
            });
            # isDelete
            \Delorius\Core\ORM::extensionMethod("isDelete",function($_orm) use($user){
                return $user->isAllowed($_orm->table_name(),"delete");
            });
        ');

    }


}


