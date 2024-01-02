<?php

namespace CMS\Core\Component\Header;

use CMS\Core\Entity\Meta;
use Delorius\Behaviors\ControllerBehavior;
use Delorius\Core\Environment;
use Delorius\Http\Response;
use Delorius\Utils\Strings;
use Delorius\View\Html;


class HeaderControllerBehavior extends ControllerBehavior
{

    /**
     * @var HeaderControl
     * @service header
     * @inject
     */
    public $header;

    /**
     * @return HeaderControl
     */
    public function getHeader()
    {
        return $this->header;
    }

    public function setMeta(Meta $meta = null, $addi = array())
    {
        $meta = ($meta == null) ? new Meta : $meta;

        if (!empty($meta->redirect)) {
            $this->getOwner()->httpResponse->redirect($meta->redirect, Response::S301_MOVED_PERMANENTLY);
        }

        if (is_array($addi) && count($addi)) {

            $altTitle = $addi['title'];
            $altDesc = $addi['desc'];

            $property = $addi['property'];
        }

        $header = $this->getHeader();

        /** title processing page */
        $title = $meta->getTitle();
        if (!$title && $altTitle) {
            $title = $altTitle;
            $header->addTitle($this->clearContent($altTitle));
        } elseif ($title) {
            $header->SetTitle($this->clearContent($title), true);
        }


        if ($meta->keys)
            $header->addKeywords($meta->keys);

        $desc = $meta->getDesc();
        if (!$desc) {
            $desc = $altDesc;
        }
        if ($desc)
            $header->setDescription($this->clearContent($desc));


        #prefix
        #og: http://ogp.me/ns#
        $property_og = $meta->getOptions('og');
        $self_title = false;
        $self_desc = false;
        foreach ($property_og as $opt) {
            if ($opt->value) {
                $property[$opt->code . ':' . $opt->name] = Strings::escape($meta->parserData($opt->value));
                if ($opt->name == 'title') {
                    $self_title = true;
                }
                if ($opt->name == 'description') {
                    $self_desc = true;
                }
            }
        }

        if (
            (!$property['og:title'] && $title) ||
            ($property['og:title'] && $meta->isChangeTitle() && !$self_title)
        ) {
            $property['og:title'] = $title;
        }

        if (
            (!$property['og:description'] && $desc) ||
            ($property['og:description'] && $meta->isChangeDesc() && !$self_desc)
        ) {
            $property['og:description'] = $desc;
        }

        $property['og:title'] = $this->clearContent($property['og:title']);
        $property['og:description'] = $this->clearContent($property['og:description']);

        if ($property['og:image']) {
            $property['og:image'] = \CMS\Core\Helper\Helpers::canonicalUrl($property['og:image']);
        }

        if (!$property['og:url'])
            $property['og:url'] = $this->getOwner()->urlScript->getAbsoluteUrlNoQuery();

        foreach ($property as $name => $value) {
            if ($value)
                $header->setProperty($name, $value);
        }
        #end prefix
    }

    /**
     * @param $text
     * @param int $max
     * @return string
     */
    protected function clearContent($text, $max = 200)
    {
        $text = $this->getParser()->html($text);
        return Strings::truncate(Html::clearTags($text), $max);
    }

    private $_parser;

    /**
     * @return object
     * @throws \Delorius\Exception\Error
     * @throws \Delorius\Exception\MissingService
     */
    public function getParser()
    {
        if (!$this->_parser) {
            $this->_parser = Environment::getContext()->getService('parser');
        }

        return $this->_parser;
    }

}