<?php
namespace CMS\Core\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Exception\Error;

class LinkBehavior extends ORMBehavior
{
    public $router = null;
    public $params = array();

    /**
     * @param array $get
     * @return string
     */
    public function link($get = array()){
        $orm = $this->getOwner();
        $params = $get;
        if(count($this->params)){
            foreach($this->params as $name=>$value){
                $params[$name] = $orm->{$value};
            }
        }

        if($this->router == null){
            throw new Error('Parameter is not specified routing by '.get_class($orm));
        }

        return link_to($this->router,$params);
    }

} 