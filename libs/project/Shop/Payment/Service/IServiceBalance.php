<?php
namespace Shop\Payment\Service;

use Shop\Payment\Exception\ServicePaymentError;

interface IServiceBalance {

    /**
     * @return mixed
     * @throws ServicePaymentError
     */
    public function payment();

} 