<?php
namespace Shop\Payment\Callback;

use CMS\Core\Helper\Helpers;
use Delorius\Exception\Error;
use Shop\Store\Entity\Balance;
use Shop\Store\Entity\Bill;
use Shop\Store\Exception\BalanceError;

class BillCallback extends PaymentCallback {

    /**
     * @return void
     * @throws \Delorius\Exception\Error
     */
    public function paid()
    {
        try{
            $bill = $this->getBill();
            Balance::getByUserId($bill->user_id)->addfunds($this->account->value,$this->account->desc);
            $bill->status = Bill::STATUS_PAID;
            $bill->save();
            $this->accountPaid();
        }catch (BalanceError $e){
            throw new Error($e->getMessage());
        }

    }

    /**
     * @return mixed
     */
    public function success()
    {
        return '';
    }

    /**
     * @return mixed
     */
    public function fail()
    {
       return '';
    }

    /**
     * @return Bill
     * @throws \Delorius\Exception\Error
     */
    protected function getBill(){
        $bill = new Bill($this->account->target_id);
        if($this->account->target_type != Helpers::getTableId($bill)) {
            throw new Error(_sf('Account #{0} does not belong to this {1}',
                $this->account->pk(),
                get_class($bill)
            ));
        }
        return $bill;
    }
}