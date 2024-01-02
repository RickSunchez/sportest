<?php
namespace CMS\Core\Component\Snippet\Controls;

use  CMS\Core\Component\Snippet\AParserRenderer;

class LinkTag extends AParserRenderer {

    public function render(){
        $absolute = isset($this->query['absolute'])?$this->query['absolute']:true;
        unset($this->query['absolute']);
        $link = link_to($this->path,$this->query,$absolute);
        return $link;
    }

}