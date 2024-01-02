<?php
namespace Delorius\Page\Breadcrumb\Controls;

use Delorius\Utils\Strings;

class LinkControl extends BaseControl{

    /**
     * @var  string Url
     */
    protected $url;

    /**
     * @var string
     */
    protected $title;

    public function __construct($name,$url,$title){
        parent::__construct($name);
        $this->url = $url;
        $this->title = $title;
    }

    public function getUrl(){
        return $this->url;
    }

    public function getTitle(){
        return Strings::firstUpper($this->title);
    }

} 