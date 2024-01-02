<?php
namespace Delorius\Page\Menu\Controls;

class LinkControl extends BaseControl{

    /** @var  string Url  */
    protected $url;

    public function __construct($name,$url){
        parent::__construct($name);
        $this->url = $url;
    }

    public function getUrl(){
        return $this->url;
    }

} 