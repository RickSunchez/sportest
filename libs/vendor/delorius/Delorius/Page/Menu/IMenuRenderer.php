<?php
namespace Delorius\Page\Menu;


interface IMenuRenderer {

    /**
     * Provides complete form rendering.
     * @return string
     */
    function render(MenuBuilder $menu);

} 