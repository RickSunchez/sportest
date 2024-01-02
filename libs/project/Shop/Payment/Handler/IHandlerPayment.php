<?php
namespace Shop\Payment\Handler;

use Delorius\View\Html;

interface IHandlerPayment
{

    /**
     * @return string|Html
     */
    public function render();

    /**
     * @param array $trans
     * @return mixed
     */
    public function result($trans = array());

    /**
     * @param array $trans
     * @return mixed
     */
    public function success($trans = array());

    /**
     * @param array $trans
     * @return mixed
     */
    public function fail($trans = array());


}