<?php
namespace CMS\Mail\Model\Notification;

use CMS\Mail\Entity\Subscriber;
use Delorius\Core\Environment;

class NotifySubscriber extends Notify
{
    /**  @var \CMS\Mail\Entity\Subscriber */
    private $_subscriber;
    /** @var int */
    private $_groupId = 0;


    public function __construct(Subscriber $subscriber)
    {
        $this->_subscriber = $subscriber;
    }

    public function setGroupId($groupId)
    {
        $this->_groupId = $groupId;
        return $this;
    }

    public function send($subject, $message)
    {
        $mailer = $this->getMailer();
        $mailer->Subject = $subject;
        $mailer->AddAddress(
            $this->_subscriber->email,
            $this->_subscriber->name
        );
        $unsub = '<br /><br >
                <div>Вы получили это письмо, так как подписаны на рассылку.</div>
                <div>Если Вы не желаете больше получать письма этой рассылки, Вы можете отписаться от неё:
                <a href="' . link_to('mail_unsub', array('hash' => $this->_subscriber->hash, 'group_id' => $this->_groupId)) . '">отписаться от рассылки</a>.</div>
            ';
        $config = Environment::getContext()->getParameters('mail');
        $message .= $config['footer'] . $unsub;
        foreach ($this->_subscriber as $name => $value) {
            if ($name == 'name' && empty($value))
                $value = 'Друг';

            $message = str_replace('[' . $name . ']', $value, $message);
        }
        $mailer->Body = $message;
        $mailer->action_function = array($this,'logger');
        return $mailer->Send();
    }
} 