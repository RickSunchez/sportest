<?php
namespace CMS\Mail\Model;


Class Mail extends \PHPMailer
{

    /** @var array(string) */
    protected $signature = array();


    public function __construct($exceptions = false, $config)
    {
        parent::__construct($exceptions);
        switch ($config['type']) {
            case 'smtp':
                $this->IsSMTP(); // set mailer to use SMTP
                if ($config['smtp']['auth'])
                    $this->SMTPAuth = $config['smtp']['auth'];
                if ($config['smtp']['secure'])
                    $this->SMTPSecure = $config['smtp']['secure'];
                if ($config['smtp']['host'])
                    $this->Host = $config['smtp']['host'];
                if ($config['smtp']['port'])
                    $this->Port = $config['smtp']['port'];
                if ($config['smtp']['user'])
                    $this->Username = $config['smtp']['user'];
                if ($config['smtp']['password'])
                    $this->Password = $config['smtp']['password'];
                break;
            case 'sendmail':
                $this->IsSendmail();
                break;
            case 'mail':
            default:
                $this->IsMail();
                break;
        }

        if ($config['charset'])
            $this->CharSet = $config['charset'];

        $this->IsHTML();
        $this->AltBody = "To view the message, please use an HTML compatible email viewer!";
        /* адрес отсылатиля*/
        $this->SetFrom($config['from']['email'], $config['from']['name']);
        $this->SMTPDebug = 0;
        $this->signature = (array)$config['signature'];
    }

    /**
     * @return array(string)
     */
    public function getSignature()
    {
        return (array)$this->signature;
    }
}