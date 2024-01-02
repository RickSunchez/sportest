<?php
namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use Delorius\Core\Environment;

class ConfigTag extends AParserRenderer
{


    public function render()
    {
        return Environment::getContext()->getService('config')->get($this->path);
    }

}