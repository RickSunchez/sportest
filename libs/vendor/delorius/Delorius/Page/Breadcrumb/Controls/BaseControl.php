<?php
namespace Delorius\Page\Breadcrumb\Controls;

use Delorius\ComponentModel\Component;
use Delorius\Core\Object;
use Delorius\Page\Breadcrumb\IControl;


class BaseControl extends Component implements IControl{

    /** @var  string */
    protected $caption;
    /** @var array Атребуте котороые необходимо добавить элементу */
    protected $attributes = array();

    public function __construct($caption = null){
        $this->caption = $caption;
    }

    /**
     * Changes control's HTML attribute.
     * @param  string name
     * @param  mixed  value
     * @return BaseControl  provides a fluent interface
     */
    public function setAttribute($name, $value = TRUE)
    {
        $this->attributes[$name] = $value;
        return $this;
    }
    /**
     * Changes control's HTML attribute.
     * @param  array Attributes
     * @param  mixed  value
     * @return BaseControl  provides a fluent interface
     */
    public function setAttributes($arr)
    {
        $this->attributes += $arr;
        return $this;
    }

    public function getCaption(){
        return $this->caption;
    }

    public function getAttributes(){
        return $this->attributes;
    }

} 