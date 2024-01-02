<?php
namespace CMS\Mail\Model\Notification\Bridges;

use Delorius\DI\CompilerExtension;


class MailExtension extends CompilerExtension
{
    public $defaults = array(
        'exceptions' => false,
        'debugger' => false,
        'type' => 'mail', // smtp,sendmail,mail
        'charset' => 'UTF-8',
        'smtp' => array(),
        'from' => array(
            'name' => null,
            'email' => null,
        ),
        'system' => array(
            'name' => null,
            'email' => null,
            'cc' => array( // копии писем
//                array('mail1@test.ru','User 1'),
//                array('mail2@test.ru','User 2'),
            )
        ),
        'signature' => array()

    );

    public function loadConfiguration()
    {
        $configs = $this->validateConfig($this->defaults);
        $container = $this->getContainerBuilder();
        $container->addDefinition($this->prefix('mail'))
            ->setClass('CMS\Mail\Model\Mail')
            ->setArguments(array(
                $configs['exceptions'],
                $configs
            ));

        $container->addDefinition($this->prefix('notify'))
            ->setClass('CMS\Mail\Model\Notification\Notify')
            ->setFactory('CMS\Mail\Model\Notification\NotifySender', array($configs['debugger']));

        $system = $container->addDefinition($this->prefix('notifyAdmin'))
            ->setClass('CMS\Mail\Model\Notification\NotifySystem')
            ->setArguments(array($configs['debugger']))
            ->addSetup('setAddressee', array($configs['system']['email'], $configs['system']['name']));

        if (count($configs['system']['cc'])) {
            foreach ($configs['system']['cc'] as $name => $email) {
                $system->addSetup('addCC', array($email, $name));
            }
        }

        if ($this->name === 'mail') {
            $container->addAlias('mail', $this->prefix('mail'));
            $container->addAlias('PHPMailer', $this->prefix('mail'));
            $container->addAlias('notify', $this->prefix('notify'));
            $container->addAlias('notify.sender', $this->prefix('notify'));
            $container->addAlias('notify.system', $this->prefix('notifyAdmin'));
            $container->addAlias('notify.admin', $this->prefix('notifyAdmin'));
        }

    }


}
