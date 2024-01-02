<?php
namespace CMS\Mail\Model\Notification;

use CMS\Core\Component\Register;
use CMS\Core\Helper\Visitor;

class NotifySystem extends Notify
{

    public function send($subject, $message)
    {
        $mailer = $this->getMailer();
        $mailer->ClearAddresses();
        $mailer->AddAddress(
            $this->email,
            $this->to
        );
        $mailer->Subject = $subject;
        $message .= Visitor::info();
        $mailer->Body = $message;
        $mailer->action_function = array($this, 'logger');

        $this->register->add(
            Register::TYPE_INFO,
            Register::SPACE_SITE,
            'Вам письмо: "[subject]"',
            null,
            array(
                'subject' => $subject
            )
        );

        return $mailer->Send();
    }


}