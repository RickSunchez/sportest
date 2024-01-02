<?php
namespace CMS\Mail\Model;

use CMS\Mail\Model\Mail;
use CMS\Mail\Entity\SubscriberGroup;
use CMS\Mail\Entity\Subscriber;
use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Exception\Error;
use Delorius\Utils\Strings;


class SubscriberBuilder extends Object {

    /** @var  \CMS\Mail\Entity\Subscriber */
    private $_sub;

    /** @return SubscriberBuilder */
    public static function factory($facts){
        $class = get_called_class();
        $builder = new $class($facts);
        return $builder;
    }

    public function __construct($facts){
        if(is_array($facts)) {
            $this->initAsArray($facts);
        }else if($facts instanceof Subscriber){
            $this->initAsObject($facts);
        }else if(is_string($facts)){
            $this->initAsString($facts);
        }else if(is_int($facts)){
            $this->initAsInt($facts);
        }
        else
            throw new Error("Identity not SubscriberBuilder");
    }

    /** @var  \CMS\Mail\Entity\Subscriber */
    public function getOwner(){
        return $this->_sub;
    }

    protected function initAsArray(array $facts){
        $httpRequest = Environment::getContext()->getService('httpRequest');
        $this->_sub = new Subscriber();
        $this->_sub->where('email','=',$facts['email'])->find();
        if(!$this->_sub->loaded()){
            $this->_sub->name = $facts['name'];
            $this->_sub->email = Strings::lower($facts['email']);
            $this->_sub->status = 1;
            $this->_sub->hash = Strings::random(45);
            $this->_sub->ip = $httpRequest->getRemoteAddress();
            $this->_sub->save(true);
        }else{
            $this->_sub->name = $facts['name']?$facts['name']:$this->_sub->name;
            $this->_sub->ip = $httpRequest->getRemoteAddress();
            $this->_sub->status = 1;
            $this->_sub->save(true);
        }
    }

    protected function initAsObject(Subscriber $sub){
        $this->_sub = $sub;
    }

    protected function initAsString($hash){
        $this->_sub = new Subscriber();
        $this->_sub->where('hash','=',$hash)->find();
    }

    protected function initAsInt($id){
        $this->_sub = new Subscriber($id);
    }
} 