<?php
namespace Shop\Payment\Behaviors;

use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\Error;
use Delorius\Exception\OrmValidationError;
use Delorius\Utils\Strings;
use Shop\Payment\Entity\Account;

class AccountBehavior extends ORMBehavior
{
    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $_logger;

    /** @var  string */
    public $callback;
    /** @var string */
    public $field_price = 'value';
    /** @var string */
    public $desc = '';
    /** @var array */
    public $setting = array();


    /**
     * @param string $callback
     * @return ORM
     */
    public function setCallbackPayment($callback)
    {
        $this->callback = $callback;
        return $this->getOwner();
    }

    /**
     * @param string $desc
     * @return ORM
     */
    public function setDescriptionPayment($desc)
    {
        $this->desc = $desc;
        return $this->getOwner();
    }

    /**
     * @param array $setting
     * @return ORM
     */
    public function settingPayment(array $setting)
    {
        $this->setting = $setting;
        return $this->getOwner();
    }

    /**
     * @return Account
     */
    public function getAccount()
    {
        return Account::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', Helpers::getTableId($this->getOwner()))
            ->order_created('desc')
            ->find();
    }

    /**
     * @var bool
     */
    protected $loaded;

    public function beforeSave(ORM $orm)
    {
        $this->loaded = $orm->loaded();
        if (!$this->loaded && !$this->callback) {
            throw new Error(_sf('Not set callback by {0} before save', get_class($orm)));
        }
    }

    public function afterSave(ORM $orm)
    {
        if (!$this->loaded) {
            try {
                $account = new Account();
                $account->callback = $this->callback;
                $account->target_id = $orm->pk();
                $account->target_type = Helpers::getTableId($orm);
                $account->value = $orm->{$this->field_price};
                $account->status = Account::STATUS_NEW;
                $account->setConfig($this->setting);
                $account->desc = Strings::replace($this->desc, $this->getPattern());
                $account->save();
            } catch (OrmValidationError $e) {
                $this->_logger->error($e->getErrorsMessage(), get_class($orm));
            }
        } else {
            $account = Account::model()
                ->where('target_id', '=', $orm->pk())
                ->where('target_type', '=', Helpers::getTableId($orm))
                ->find();
            if ($account->loaded()) {
                $account->value = $orm->{$this->field_price};
                $account->save();
            }
        }
    }

    public function afterDelete(ORM $orm)
    {
        $account = $this->getAccount();
        if ($account->loaded()) {
            $account->status = Account::STATUS_DELETE;
        }
    }

    /**
     * @var array
     */
    private $_pattern = array();

    protected function getPattern()
    {
        if (!sizeof($this->_pattern)) {
            $object = $this->getOwner()->as_array();
            foreach ($object as $name => $value) {
                $this->_pattern['#\{' . $name . '\}#'] = $value;
            }
        }
        return $this->_pattern;
    }

}