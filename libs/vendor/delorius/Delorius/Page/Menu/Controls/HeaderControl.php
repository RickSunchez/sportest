<?php
namespace Delorius\Page\Menu\Controls;

class HeaderControl extends BaseControl{

    /** @var \SplObjectStorage */
    protected $controls;

    public function __construct($name){
        parent::__construct($name);
        $this->controls =  new \SplObjectStorage;
    }

    /** @return \Delorius\Page\Menu\Controls\HeaderControl */
    public function addLink($name,$url){
        $link = new LinkControl($name,$url);
        $this->controls->attach($link);
        return $this;
    }

    /** @return \Delorius\Page\Menu\Controls\HeaderControl */
    public function addLinkRoute($name,$route){
        $link = new LinkControl($name,$this->getLink($route));
        $this->controls->attach($link);
        return $this;
    }

    public function getControls()
    {
        return iterator_to_array($this->controls);
    }


    protected  function getLink($route){
        list($path,$query) = explode('?',$route);
        if($query){
            parse_str($query,$out);
        }
        $link =  link_to($path,(array)$out);
        return $link;
    }

} 