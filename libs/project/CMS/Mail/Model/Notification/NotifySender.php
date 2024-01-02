<?php
namespace CMS\Mail\Model\Notification;

use Delorius\Exception\InvalidArgument;

class NotifySender extends Notify
{

    public function send($subject, $message)
    {
        if (!$this->isInit()) {
            throw new InvalidArgument('Вы не указали адресата NotifySender::setAddressee($email, $to = null)');
        }
        $mailer = $this->getMailer();
        $mailer->ClearCCs();
        $mailer->ClearAddresses();
        $mailer->AddAddress(
            $this->email,
            $this->to
        );
        $mailer->Subject = $subject;
        $mailer->Body = $message . $this->getSignature();
        $mailer->action_function = array($this, 'logger');
        $this->clean();
        return $mailer->Send();
    }


} 