<?php
namespace Shop\Payment\Callback;

interface ICallbackPayment
{

    /**
     * @return void
     * @throws \Delorius\Exception\Error
     */
    public function paid();

    /**
     * @return mixed
     */
    public function success();

    /**
     * @return mixed
     */
    public function fail();


} 