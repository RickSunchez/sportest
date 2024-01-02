<?php
namespace Shop\Core\Bridges;

use Delorius\DI\CompilerExtension;
use Shop\Commodity\Helpers\Popular;


class ShopExtension extends CompilerExtension
{

    public function loadConfiguration()
    {
        $container = $this->getContainerBuilder();
        $config = $this->getConfig();
        $container->parameters['shop'] = $config;

        $container->addDefinition($this->prefix('currency'))
            ->setClass('\Shop\Store\Model\CurrencyBuilder');

        $container->addDefinition($this->prefix('storageBasket'))
            ->setClass('\Shop\Store\Component\Cart\IStorageBasket')
            ->setFactory('\Shop\Store\Component\Cart\Storage\SessionStorageBasket');

        $container->addDefinition($this->prefix('basket'))
            ->setClass('\Shop\Store\Component\Cart\Basket');


        if ($this->name === 'shop') {
            $container->addAlias('currency', $this->prefix('currency'));
            $container->addAlias('storageBasket', $this->prefix('storageBasket'));
            $container->addAlias('basket', $this->prefix('basket'));
        }
    }


    public function afterCompile(\Delorius\PhpGenerator\ClassType $class)
    {
        $initialize = $class->getMethod('initialize');
        $initialize->addBodyClass($this);

        $currency = $this->prefix('currency');
        $initialize->addBody('$this->getService(?)->setting();', array($currency));

        $basket = $this->prefix('basket');
        $initialize->addBody('$this->getService(?)->onAddItem[] = 
            function ($basket, $id, $options, $quantity, $set, $type)  {
                Shop\Commodity\Helpers\Popular::add_cart($id);        
            };', array($basket));

    }


}
