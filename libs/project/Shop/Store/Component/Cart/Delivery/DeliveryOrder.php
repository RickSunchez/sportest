<?php
namespace Shop\Store\Component\Cart\Delivery;

use Delorius\Core\Environment;
use Shop\Store\Component\Cart\IItems;

class DeliveryOrder implements IItems
{

    protected $id;
    protected $name;
    protected $value;
    protected $desc;
    protected $status;

    public function __construct($config)
    {

        $this->id = $config['id'];
        $this->name = $config['name'];
        $this->value = $config['value'];
        $this->desc = $config['desc'];
        $this->is_active = $config['is_active'];
    }

    public function isActive()
    {
        return $this->is_active;
    }

    public function getName()
    {
        return $this->name ? $this->name : 'Доставка';
    }

    public function getDesc()
    {
        return $this->desc;
    }

    public function getId()
    {
        return $this->id;
    }

    public function getPrice($format = true)
    {
        return Environment::getContext()
            ->getService('currency')
            ->format($this->getValue(),SYSTEM_CURRENCY,null,$format);
    }

    public function getValue()
    {
        return $this->value;
    }
}