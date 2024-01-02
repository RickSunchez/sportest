<?php
namespace Delorius\Attribute;

class CollectionAttribute {

    protected $collection = array();

    public function get($name){
        if($this->issetAttribute($name)){
            return $this->collection[$name];
        }
        return null;
    }

    public function set($name, Attribute $class){
        if(!$this->issetAttribute($name)){
            $this->collection[$name] = $class ;
        }else{
            unset($this->collection[$name]);
            $this->collection[$name] = $class ;
        }
        return $this;
    }

    protected function issetAttribute($name){
        return ($this->collection[$name] instanceof Attribute);
    }

}
