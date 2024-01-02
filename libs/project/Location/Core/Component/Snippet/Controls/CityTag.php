<?php

namespace Location\Core\Component\Snippet\Controls;

use  CMS\Core\Component\Snippet\AParserRenderer;
use Delorius\Core\Environment;

class CityTag extends AParserRenderer
{

    public function render()
    {
        if ($this->path == 'name') {
            $method = 'getName' . ($this->query['v'] == 1 ? '' : $this->query['v']);
            $city = Environment::getContext()
                ->getService('city');
            $name = $city->${method}();
            if (!$name) {
                $name = $city->getName();
            }
            return $name;
        }

        if ($this->path == 'attr') {
            $name = $this->query['name'];
            $city = Environment::getContext()
                ->getService('city');
            return $city->getAttr($name);
        }


    }

}