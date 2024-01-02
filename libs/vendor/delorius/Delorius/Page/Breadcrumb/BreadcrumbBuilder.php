<?php
namespace Delorius\Page\Breadcrumb;

use Delorius\Exception\Error;
use Delorius\Page\Breadcrumb\Controls\LastItemControl;
use Delorius\Page\Breadcrumb\Controls\LinkControl;
use Delorius\Page\Breadcrumb\Rendering\DefaultBreadcrumbRenderer;
use Delorius\Page\Container;
use Delorius\View\Html;

class BreadcrumbBuilder extends Container{

    /** @var \Delorius\Page\Breadcrumb\IBreadcrumbRenderer */
    protected  $renderer;
    /** @var \SplObjectStorage */
    protected $controls;
    /** @var  \Delorius\Page\Breadcrumb\Controls\LinkControl */
    protected $first;
    /** @var  \Delorius\Page\Breadcrumb\Controls\LastItemControl */
    protected $last;

    public function __construct($name){
        parent::__construct(NULL, $name);
        $this->controls =  new \SplObjectStorage;
    }

    /** @return \Delorius\Page\Breadcrumb\BreadcrumbBuilder */
    public function addLink($name,$url,$title = null,$isRoute = true){
        if($isRoute){
            $url = $this->getLink($url);
        }
        $link = new LinkControl($name,$url,$title?$title:$name);
        $this->controls->attach($link);
        return $this;
    }

    /** @return \Delorius\Page\Breadcrumb\BreadcrumbBuilder */
    public function setLastItem($name){
        $this->last = new LastItemControl($name);
        return $this;
    }

    /** @return \Delorius\Page\Breadcrumb\BreadcrumbBuilder */
    public function setFirstItem($name,$url,$title = null,$isRoute = true){
        if($isRoute){
            $url = $this->getLink($url);
        }
        $this->first = new LinkControl($name,$url,$title?$title:$name);
        return $this;
    }

    /** @return \Delorius\Page\Breadcrumb\Controls\LastItemControl */
    public function getLastItem(){
        if(!$this->last)
        {
            return null;
        }
        return $this->last;
    }

    /** @var  \Delorius\Page\Breadcrumb\Controls\LinkControl */
    public function getFirstItem(){
        if(!$this->first)
        {
            throw new Error('Set not the first element');
        }
        return $this->first;
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


    /********************* rendering ****************d*g**/

    /**
     * Sets Breadcrumb renderer.
     * @return BreadcrumbBuilder  provides a fluent interface
     */
    public function setRenderer(IBreadcrumbRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Returns form renderer.
     * @return IBreadcrumbRenderer
     */
    final public function getRenderer()
    {
        if ($this->renderer === NULL) {
            $this->renderer = new DefaultBreadcrumbRenderer();
        }
        return $this->renderer;
    }


    /** @return  Html|string */
    public function render(){
        return $this->getRenderer()->render($this);
    }

    /**
     * @return Html|string
     */
    public function __toString(){
        return $this->render();
    }




}