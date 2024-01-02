<?php
namespace CMS\SEO\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use CMS\SEO\Model\Helpers;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Utils\Arrays;

class TmpTextTag extends AParserRenderer
{
    public function render()
    {
        $data = array();
        $ormId = $this->path;
        $ownerId = isset($this->query['ownerId']) ? $this->query['ownerId'] : null;

        if (Arrays::keysExists('guid', $this->query)) {
            $GUID = Environment::getContext()->getService('site')->GUID;
            if ($GUID) {
                $ownerId = $GUID;
            }
        }


        if (Arrays::keysExists('data', $this->query)) {
            $name = $this->query['data'];
            if ($object = Environment::getContext()->getService('site')->{$name}) {
                if ($object instanceof ORM) {
                    $object = $object->as_array();
                }
                $data = $object;
            }

        }

        return Helpers::getText($ormId, $ownerId, $data);
    }

}