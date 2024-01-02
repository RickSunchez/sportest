<?php
namespace Shop\Payment\Callback;

use Delorius\Exception\Error;
use Shop\Payment\Entity\Account;

abstract class PaymentCallback implements ICallbackPayment
{

    /** @var \Shop\Payment\Entity\Account */
    protected $account;

    public function __construct(Account $account)
    {
        if (!$account->loaded()) {
            throw new Error(_sf('Account must be loaded to {0}', get_class($this)));
        }
        $this->account = $account;
    }

    protected function accountPaid()
    {
        $this->account->date_paid = time();
        $this->account->status = Account::STATUS_SUCCESS;
        $this->account->save();
    }

    protected function accountFail()
    {
        $this->account->status = Account::STATUS_FAIL;
        $this->account->save();
    }


}