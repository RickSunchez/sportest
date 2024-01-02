<?php
namespace CMS\Mail\Model\Notification;

use CMS\Core\Component\Register;
use CMS\Mail\Model\Mail;
use Delorius\Core\Object;
use Delorius\Tools\ILogger;

abstract class Notify extends Object
{
    /**
     * @var ILogger
     */
    private $log;

    /**
     * @var Register
     */
    protected $register;

    /**
     * @var bool
     */
    protected $debug = false;

    /**
     * @var string Email for send
     */
    protected $email;

    /**
     * @var string Name for send
     */
    protected $to;

    /**
     * Установка адресата
     * @var bool
     */
    protected $init = false;

    /** @var \CMS\Mail\Model\Mail */
    private $_mailer;


    public function __construct(
        $debug = false,
        ILogger $logger,
        Mail $mail,
        Register $register
    )
    {
        $this->log = $logger;
        $this->debug = $debug;
        $this->register = $register;
        $this->_mailer = $mail;
    }

    /** @return bool */
    abstract function send($subject, $message);


    /**
     * @param string $email
     * @param string|null $to
     * @return $this
     */
    public function setAddressee($email, $to = null)
    {
        $this->email = $email;
        $this->to = $to;
        $this->init = true;
        return $this;
    }

    /**
     * @param sting $email
     * @param null|string $name
     * @return $this
     * @throws \phpmailerException
     */
    public function setFrom($email, $name = null)
    {
        $this->getMailer()->SetFrom($email,$name);
        return $this;
    }

    public function isInit()
    {
        return $this->init;
    }

    /**
     * @param string $path путь до файла
     * @param string $name название файла
     * @return $this
     */
    public function addFile($path, $name)
    {
        $this->getMailer()->AddAttachment($path, $name);
        return $this;
    }

    /**
     * @param string $mail Mail
     * @param string $name Name
     * @return $this
     */
    public function addCC($mail, $name)
    {
        $this->getMailer()->AddCC($mail, $name);
        return $this;
    }


    /** @return \CMS\Mail\Model\Mail */
    protected function getMailer()
    {
        return $this->_mailer;
    }

    protected function getSignature()
    {
        $signature = $this->getMailer()->getSignature();
        $s = '';
        foreach ($signature as $str) {
            $s .= $str;
        }
        return $s;
    }

    /**
     * @return $this
     */
    protected function clean()
    {
        $this->email = null;
        $this->to = null;
        $this->init = false;
        return $this;
    }

    /**
     * @param $isSent
     * @param $to
     * @param $cc
     * @param $bcc
     * @param $subject
     * @param $body
     */
    public function logger($isSent, $to, $cc, $bcc, $subject, $body)
    {
        if ($this->debug) {
            $this->log->info(var_export(func_get_args(), true), 'NotifySystem');
        }
    }

} 