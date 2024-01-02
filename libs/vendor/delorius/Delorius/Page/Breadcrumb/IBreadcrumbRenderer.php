<?php
namespace Delorius\Page\Breadcrumb;


interface IBreadcrumbRenderer {

    /**
     * Provides complete form rendering.
     * @return string
     */
    function render(BreadcrumbBuilder $Breadcrumb);

} 