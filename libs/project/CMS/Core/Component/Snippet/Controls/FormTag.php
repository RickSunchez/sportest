<?php
namespace CMS\Core\Component\Snippet\Controls;

use  CMS\Core\Component\Snippet\AParserRenderer;
use Delorius\View\View;

class FormTag extends AParserRenderer
{

    public function render()
    {
        $view = new View();
        return $view->load('form/' . $this->path);
    }

}