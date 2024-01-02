<?php
namespace Shop\Store\Component\Cart\Payment;


use Delorius\Exception\Error;
use Shop\Payment\Handler\RobokassaHandler;
use Shop\Store\Component\Cart\OrderCart;

class PaymentOnline extends PaymentBase
{
    /** @var \Shop\Payment\Handler\HandlerPayment  */
    private $_handler;

    /**
     * @return \Shop\Payment\Handler\HandlerPayment
     * @throws \Delorius\Exception\Error
     */
    protected function getHandler(){
        if(!$this->_handler){
            if($this->cart instanceof OrderCart ){
                $this->_handler = new RobokassaHandler($this->cart->getOwner()->getAccount());
            }
            else{
                throw new Error(_sf('Handler only works with the Order, and then ',get_class($this->cart)));
            }
        }
        return $this->_handler;
    }

    public function render(){
        return $this->getHandler()->render();
    }
}