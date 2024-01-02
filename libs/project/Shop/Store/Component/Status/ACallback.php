<?php
namespace Shop\Store\Component\Status;

use Shop\Store\Entity\Order;

abstract class ACallback
{

    /** @var  Order */
    protected $order;

    public function __construct(Order $orm)
    {
        $this->order = $orm;
    }

    abstract function run();

}