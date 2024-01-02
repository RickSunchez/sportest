<?php

namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Meta;
use CMS\Core\Helper\ParserString;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Utils\Strings;

class MetaDataBehavior extends ORMBehavior
{


    /**
     * @var string Title template
     */
    public $title;

    /**
     * @var string Description template
     */
    public $desc;

    /**
     * length desc
     * @var int
     */
    protected $length = 250;

    /** @return \CMS\Core\Entity\Meta для текущей модели */
    public function getMeta()
    {
        $meta = Meta::loadByOwner($this->getOwner());
        $parser = new ParserString($this->getOwner()->as_array());

        $tmpTitle = $meta->title ? $meta->title : $this->title;
        if ($tmpTitle) {
            $title = $parser->render($tmpTitle);
            $meta->setTitle($title);
        }

        $tmpDesc = $meta->desc ? $meta->desc : $this->desc;
        if ($tmpDesc) {
            $desc = $parser->render($tmpDesc);

            $desc = Strings::truncate($desc, $this->length);
            $meta->setDesc($desc);
        }
        $meta->setParser($parser);
        return $meta;
    }

    public function afterDelete(ORM $orm)
    {
        $meta = $this->getMeta();
        if ($meta->loaded()) {
            $meta->delete();
        }
    }

} 