<?php
namespace Delorius\Page\Pagination;

interface IHandlerPagination{

    /** @return результат работы */
    function result();

    /** @return int кол-во записей */
    function count();

    function limit($limit);

    function offset($offset);

}