<?php
namespace Shop\Store\Behaviors;

use Shop\Store\Entity\Cashflow;
use Shop\Store\Exception\BalanceError;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;

class CashflowBehavior extends ORMBehavior
{

    /**
     * @var \Delorius\Tools\ILogger
     * @service logger
     * @inject
     */
    public $_logger;

    /**
     * @var \Delorius\Http\IRequest
     * @service httpRequest
     * @inject
     */
    public $_httpRequest;


    /**
     * Withdraw
     * @param float $value
     * @param string $reason
     * @throws BalanceError
     */
    public function withdraw($value, $reason)
    {
        /** @var ORM $balance */
        $balance = $this->getOwner();
        if (!$balance->loaded()) {
            throw new BalanceError(_t('CMS:Users', 'Balance of the user is not loaded'));
        }

        if(!$this->hasAmount($value)){
            throw new BalanceError(_t('CMS:Users', 'Insufficient Funds'));
        }

        if(!$reason){
            throw new BalanceError(_t('CMS:Users', 'Specify the grounds for payment'));
        }

        try{

            $cashflow = new Cashflow();
            $cashflow->balance_id = $balance->pk();
            $cashflow->user_id = $balance->user_id;
            $cashflow->value = $value;
            $cashflow->type = Cashflow::MINUS;
            $cashflow->reason = $reason;
            $cashflow->ip = $this->_httpRequest->getRemoteAddress();
            $cashflow->save();

            $balance->value = $balance->value - $cashflow->value;
            $balance->save();

            $this->_logger->info(_sf(
                'Снято с баланса #balance_{0}. Сумма: {1}{2} , остаток: {3} ',
                $balance->pk(),
                $cashflow->getNameTransactionTypes(),
                $cashflow->value,
                $balance->value
                ),'balance');

        }catch (OrmValidationError $e){
            throw new BalanceError(_t('CMS:Users', 'Unable to withdraw money'));
        }

    }

    /**
     * Add Funds
     * @param $value
     * @param $reason
     * @throws \Shop\Store\Exception\BalanceError
     */
    public function addfunds($value, $reason)
    {
        /** @var ORM $balance */
        $balance = $this->getOwner();
        if (!$balance->loaded()) {
            throw new BalanceError(_t('CMS:Users', 'Balance of the user is not loaded'));
        }

        if(!$reason){
            throw new BalanceError(_t('CMS:Users', 'Specify the grounds for payment'));
        }

        try{

            $cashflow = new Cashflow();
            $cashflow->balance_id = $balance->pk();
            $cashflow->user_id = $balance->user_id;
            $cashflow->value = $value;
            $cashflow->type = Cashflow::PLUS;
            $cashflow->reason = $reason;
            $cashflow->ip = $this->_httpRequest->getRemoteAddress();
            $cashflow->save();

            $balance->value = $balance->value + $cashflow->value;
            $balance->save();

            $this->_logger->info(_sf(
                'Зачислено на баланс #balance_{0}. Сумма: {1}{2} , остаток: {3} ',
                $balance->pk(),
                $cashflow->getNameTransactionTypes(),
                $cashflow->value,
                $balance->value
            ),'balance');

        }catch (OrmValidationError $e){
            throw new BalanceError(_t('CMS:Users', 'It is impossible to deposit money'));
        }
    }

    /**
     * @param float $value
     * @return bool
     */
    public function hasAmount($value){

        if($this->getOwner()->value < $value){
            return false;
        }
        return true;
    }

    /**
     * @param $start
     * @param $end
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getCashflow($start, $end = null)
    {
        $start = strtotime($start);

        if($end == null){
            $end = time();
        }else{
            $end = strtotime($end);
        }
        return Cashflow::model()
            ->where('balance_id','=',$this->getOwner()->pk())
            ->where('date_cr','BETWEEN',array($start,$end))
            ->find_all();
    }


}