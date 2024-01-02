<?php
namespace Shop\Payment\Service;

use CMS\Users\Entity\User;
use Delorius\Core\Environment;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Strings;
use Shop\Payment\Entity\Account;
use Shop\Store\Entity\Balance;

abstract class BalanceService implements IServiceBalance
{
    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $_logger;

    /** @var  int  */
    protected $userId;

    /********* account ************/
    public $settings;

    /** @var \Shop\Store\Entity\Balance */
    private $_balance = null;

    public function __construct($userId)
    {
        $this->userId = $userId;
        Environment::getContext()->callInjects($this);
    }

    /**
     * @param array $setting
     * @return $this
     */
    public function setSettings(array $setting){
        $this->settings = $setting;
        return $this;
    }

    /**
     * @return Balance
     */
    protected function getBalance()
    {
        if (!$this->_balance) {
            $this->_balance = Balance::getByUserId($this->userId);
        }
        return $this->_balance;
    }


    /**
     * @param $value
     * @param $callback
     * @param array $config
     * @param $desc
     * @return Account
     * @throws OrmValidationError
     */
    protected function createAccount($value, $callback, $config = array(), $desc)
    {
            $account = new Account();
            $account->value = $value;
            $account->target_id = $this->userId;
            $account->target_type = User::model()->table_name();
            $account->callback = $callback;
            $account->status = Account::STATUS_NEW;
            $account->setConfig($config);
            $account->desc = Strings::replace($desc, $this->getPattern($config));
            $account->save();
            return $account;
    }


    /**
     * @return mixed
     */
    protected function getPattern(array $config)
    {
        $pattern = array();
        foreach ($config as $name => $value) {
            $pattern['#\{' . $name . '\}#'] = $value;
        }
        return $pattern;
    }
} 