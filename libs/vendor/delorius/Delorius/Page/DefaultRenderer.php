<?php
namespace Delorius\Page;

use Delorius\Core\Object;
use Delorius\View\Html;

class DefaultRenderer extends Object {

    /** @var array of HTML tags */
    protected $wrappers = array(
        'pagination' => array(
            'container' => 'div',
        )
    );

    /**
     * @param  string
     * @return \Delorius\View\Html
     */
    protected function getWrapper($name)
    {
        $data = $this->getValue($name);
        return $data instanceof Html ? clone $data : Html::el($data);
    }


    /**
     * @param  string
     * @return string
     */
    protected function getValue($name)
    {
        $name = explode(' ', $name);
        $data = & $this->wrappers[$name[0]][$name[1]];
        return $data;
    }

} 