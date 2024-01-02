<?php
namespace Delorius\Page\Menu;

use Delorius\Exception\Error;
use Delorius\Page\Container;
use Delorius\Page\Menu\Controls\HeaderControl;
use Delorius\Page\Menu\Rendering\DefaultMenuRenderer;
use Delorius\View\Html;

class MenuBuilder extends Container{

    /** @var \Delorius\Page\Menu\IMenuRenderer */
    protected  $renderer;


    /** @return \Delorius\Page\Menu\Controls\HeaderControl  */
    public function addHeader($name,$caption){
        return $this[$name] = new HeaderControl($caption);
    }

    /** @return \Delorius\Page\Menu\Controls\HeaderControl  */
    public function getHeader($name){
        if(!$this->offsetExists($name)){
            throw new Error('There is no such header : '.$name);
        }
        return $this[$name];
    }

    /********************* rendering ****************d*g**/

    /**
     * Sets Menu renderer.
     * @return MenuBuilder  provides a fluent interface
     */
    public function setRenderer(IMenuRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Returns form renderer.
     * @return IMenuRenderer
     */
    final public function getRenderer()
    {
        if ($this->renderer === NULL) {
            $this->renderer = new DefaultMenuRenderer();
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