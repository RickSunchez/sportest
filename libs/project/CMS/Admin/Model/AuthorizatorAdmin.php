<?php
namespace CMS\Admin\Model;

use Delorius\Security\IAuthorizator;
use Delorius\Security\Permission;

class AuthorizatorAdmin extends Permission implements IAuthorizator{

    /** @var  \Delorius\DI\Container */
    private $conteiner;

    public function __construct(\Delorius\DI\Container $conteiner)
    {
        $this->conteiner = $conteiner;

        // задаем стандартные роли
        $this->addRole('guest'); // гость
        $this->addRole('user','guest'); // обычный пользователь
        $this->addRole('manager','user'); // редакторы
        $this->addRole('root','manager'); // супер пользователь

        // все запрещаем
        $this->deny('guest',Permission::ALL);
        $this->deny('user',Permission::ALL);
        $this->deny('manager',Permission::ALL);
        $this->deny('root',Permission::ALL);

    }

}