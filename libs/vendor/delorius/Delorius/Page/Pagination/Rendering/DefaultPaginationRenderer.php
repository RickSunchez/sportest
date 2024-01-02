<?php
namespace Delorius\Page\Pagination\Rendering;

use Delorius\Page\DefaultRenderer;
use Delorius\Page\Pagination\IPaginationRenderer;
use Delorius\Page\Pagination\PaginationBuilder;
use Delorius\View\Html;


/**
 * Class DefaultPaginationRenderer
 * @package Delorius\Page\Pagination\Rendering
 */
class DefaultPaginationRenderer extends DefaultRenderer implements IPaginationRenderer
{
    protected $all = false;

    /** @var array of HTML tags */
    protected $wrappers = array(
        'pagination' => array(
            'container' => 'div class=b-pagination',
        ),
        'page' => array(
            'container' => 'ul class="pagination hListing"',
            'item' => 'li',
            '.disabled' => 'disabled',
            '.active' => 'active'
        ),
        'all' => array(
            'container' => 'div class=all',
            'item' => 'a',
        ),
        'name' => array(
            'pre' => '&lsaquo;',
            'next' => '&rsaquo;',
            'first' => '&laquo;',
            'last' => '&raquo;',
            'all' => 'Весь список'
        )
    );

    /** @var \Delorius\Page\Pagination\PaginationBuilder */
    protected $pagination;

    /** @var int кол-во показаных траниц за раз */
    protected $count = 5;


    /**
     * Provides complete form rendering.
     * @param  \Delorius\Page\Pagination
     * @return string
     */
    public function render(PaginationBuilder $pagination)
    {
        if ($this->pagination !== $pagination) {
            $this->pagination = $pagination;
        }

        $ul = $this->getWrapper('page container');
        $arr = $this->renderBody();

        if (sizeof($arr)) {
            $ul->add($this->itemFirst());
        }

        if (sizeof($arr)) {
            $ul->add($this->itemPre());
        }

        foreach ($arr as $li) {
            $ul->add($li);
        }

        if (sizeof($arr)) {
            $ul->add($this->itemNext());
        }

        if (sizeof($arr)) {
            $ul->add($this->itemLast());
        }

        if (sizeof($arr) && $this->all) {
            $ul->add($this->itemAll());
        }

        $s = "\n\t" . $this->getWrapper('pagination container')->setHtml($ul->render());

        return $s;
    }

    /**
     * @return array
     */
    protected function renderBody()
    {
        $countPage = $this->pagination->getPageCount();
        $page = $this->pagination->getPage();
        $firstPage = $this->pagination->getFirstPage();
        $lastPage = $this->pagination->getLastPage();
        $arr = array();

        if ( /* состояние -1 */
            $countPage < 2
        ) {
            return array();
        } else
            if ( /* состояние 0 */
                (
                    abs($page - $firstPage) <= 2
                    &&
                    abs($page - $lastPage) <= 2
                )
                ||
                (
                    $countPage <= 5
                )
            ) {

                for ($i = 1; $i <= $countPage; $i++) {
                    $arr[] = $this->item($i);
                }

            } else
                if ( /* состояние 1 */
                    abs($page - $firstPage) <= 2
                    &&
                    abs($page - $lastPage) > 2
                ) {

                    for ($i = 1; $i <= 5; $i++) {
                        $arr[] = $this->item($i);
                    }

                } else if ( /* состояние 2 */
                    abs($page - $firstPage) > 2
                    &&
                    abs($page - $lastPage) > 2
                ) {
                    $left = $page - 2;
                    $right = $page + 2;

                    for ($i = $left; $i < $page; $i++) {
                        $arr[] = $this->item($i);
                    }

                    for ($i = $page; $i <= $right; $i++) {
                        $arr[] = $this->item($i);
                    }

                } else if ( /* состояние 3 */
                    abs($page - $firstPage) > 2
                    &&
                    abs($page - $lastPage) <= 2
                ) {
                    for ($i = $countPage - 4; $i <= $countPage; $i++) {
                        $arr[] = $this->item($i);
                    }

                }

        return $arr;
    }


    /**
     * @param $page
     * @return Html
     */
    protected function item($page)
    {
        $li = $this->getWrapper('page item');
        $a = Html::el('a');

        $class = 'item';
        if ($this->pagination->getPage() == $page) {
            $class .= ' ' . $this->getValue('page .active');
        }
        $li->addAttributes(array('class' => $class));
        $a->href($this->pagination->getUrlPage($page));
        $a->addAttributes(array('title' => 'Страница ' . $page));
        $a->setHtml($page);
        $li->add($a);
        return $li;
    }

    /**
     * @return Html
     */
    protected function itemFirst()
    {
        $li = $this->getWrapper('page item');
        $a = Html::el('a');
        $a->href($this->pagination->getUrlPage($this->pagination->getFirstPage()));
        $a->addAttributes(array('title' => 'Первая страница'));
        $a->setHtml($this->getValue('name first'));
        $class = 'first';
        if ($this->pagination->isFirst()) {
            $class .= ' ' . $this->getValue('page .disabled');
            $a->href('javascript:;');
        } else {
            $a->href($this->pagination->getUrlPage($this->pagination->getFirstPage()));
        }
        $li->addAttributes(array('class' => $class));
        $li->add($a);
        return $li;
    }

    /**
     * @return Html
     */
    protected function itemLast()
    {
        $li = $this->getWrapper('page item');
        $a = Html::el('a');
        $a->href($this->pagination->getUrlPage($this->pagination->getLastPage()));
        $a->addAttributes(array('title' => 'Последняя страница'));
        $a->setHtml($this->getValue('name last'));
        $class = 'last';
        if ($this->pagination->isLast()) {
            $class .= ' ' . $this->getValue('page .disabled');
            $a->href('javascript:;');
        } else {
            $a->href($this->pagination->getUrlPage($this->pagination->getLastPage()));
        }
        $li->addAttributes(array('class' => $class));
        $li->add($a);
        return $li;
    }

    /**
     * @return Html
     */
    protected function itemPre()
    {
        $li = $this->getWrapper('page item');
        $a = Html::el('a');
        $a->setHtml($this->getValue('name pre'));
        $class = 'pre';
        if ($this->pagination->isFirst()) {
            $class .= ' ' . $this->getValue('page .disabled');
            $a->href('javascript:;');
        } else {
            $a->href($this->pagination->getUrlPage($this->pagination->getPage() - 1));
            $a->addAttributes(array('title' => 'Предыдущая'));
        }
        $li->addAttributes(array('class' => $class));
        $li->add($a);
        return $li;
    }


    /**
     * @return Html
     */
    protected function itemNext()
    {
        $li = $this->getWrapper('page item');
        $a = Html::el('a');
        $a->setHtml($this->getValue('name next'));
        $class = 'next';
        if ($this->pagination->isLast()) {
            $class .= ' ' . $this->getValue('page .disabled');
            $a->href('javascript:;');
        } else {
            $a->href($this->pagination->getUrlPage($this->pagination->getPage() + 1));
            $a->addAttributes(array('title' => 'Следующая'));
        }
        $li->addAttributes(array('class' => $class));
        $li->add($a);
        return $li;
    }

    /**
     * @return Html
     */
    protected function itemAll()
    {
        $li = $this->getWrapper('page item');
        $a = Html::el('a');
        $a->href($this->pagination->getItemsAllUrlPage());
        $a->addAttributes(array('title' => $this->getValue('name all')));
        $a->setHtml($this->getValue('name all'));
        $li->addAttributes(array('class' => 'all'));
        $li->add($a);
        return $li;
    }

}