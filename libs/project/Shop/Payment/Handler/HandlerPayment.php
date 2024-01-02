<?php
namespace Shop\Payment\Handler;

use Delorius\Core\Environment;
use Shop\Payment\Entity\Account;

abstract class HandlerPayment implements IHandlerPayment
{
    const URL_SUCCESS = 'success';
    const URL_RESULT = 'result';
    const URL_FAIL = 'fail';

    /** @var \Shop\Payment\Entity\Account  */
    protected $account;

    /** @var \Delorius\Tools\ILogger */
    protected $logger;

    public function __construct(Account $account = null){
        $this->account = $account;
        $this->logger = Environment::getContext()->getService('logger');
        $this->init();
    }

    /**
     * @return void
     */
    protected function init(){}






}