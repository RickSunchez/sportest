<?php
namespace Delorius\Page\Menu\Rendering;


use Delorius\Page\DefaultRenderer;
use Delorius\Page\Menu\Controls\HeaderControl;
use Delorius\Page\Menu\IMenuRenderer;
use Delorius\Page\Menu\MenuBuilder;

/**
 * Class DefaultMenuRenderer
 * @package Delorius\Page\Menu\Rendering
 */
class DefaultMenuRenderer extends DefaultRenderer implements IMenuRenderer
{

    /** @var array of HTML tags */
    protected $wrappers = array(
        'menu' => array(
            'container' => 'div class=list-group',
        ),
        'header' => array(
            'container' => 'div',
            '.header' => 'list-group-item active i-color-white'
        ),
        'link' => array(
            'container' => 'a class=list-group-item',
            'icon' => 'i',
            '.icon_type' => 'glyphicon glyphicon-chevron-right i-right',
        )
    );

    /** @var  \Delorius\Page\Menu\MenuBuilder */
    protected $menu;


    /**
     * Provides complete form rendering.
     * @param  \Delorius\Page\Menu\MenuBuilder
     * @return string
     */
    public function render(MenuBuilder $menu)
    {
        if ($this->menu !== $menu) {
            $this->menu = $menu;
        }

        $s = '';
        foreach ($this->menu->getComponents() as $header) {
            $s .= $this->renderHeader($header);
        }
        $div = $this->getWrapper('menu container');
        $div->setHtml($s);
        return $div;
    }

    protected function renderHeader(HeaderControl $header)
    {
        $s = '';

        /** @var header $div */
        $div = $this->getWrapper('header container');
        $div->setText($header->getCaption());
        $div->addAttributes(array('class'=>$this->getValue('header .header')));
        $s .= $div->render();

        $controls = $header->getControls();
        if(sizeof($controls) == 0){
            return '';
        }
        /** add link */
        foreach ($controls as $link) {
            $icon = $this->getWrapper('link icon');
            $icon->addAttributes(array('class'=>$this->getValue('link .icon_type')));

            $a = $this->getWrapper('link container');
            $a->href($link->getUrl());
            $a->setHtml($link->getCaption().' '. $icon->render());
            $a->addAttributes($link->getAttributes());
            $s .= $a->render();
        }

        return $s;
    }


}