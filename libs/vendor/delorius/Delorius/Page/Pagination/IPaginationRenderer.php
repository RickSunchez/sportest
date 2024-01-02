<?php
namespace Delorius\Page\Pagination;


/**
 * Defines method that must implement form renderer.
 */
interface IPaginationRenderer
{

    /**
     * Provides complete pagination rendering.
     * @return string
     */
    function render(PaginationBuilder $pagination);

}
