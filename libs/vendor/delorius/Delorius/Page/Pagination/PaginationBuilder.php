<?php
namespace Delorius\Page\Pagination;

use Delorius\Core\Environment;
use Delorius\Core\Object;
use Delorius\Core\ORM;
use Delorius\Exception\Error;
use Delorius\Page\Pagination\Handler\ORMHandlerPagination;
use Delorius\Utils\Arrays;
use Delorius\View\Html;

class  PaginationBuilder extends Object
{
    const ITEMS_ALL = 'all';

    /** @var \Delorius\Page\Pagination\IHandlerPagination */
    protected $handler;
    /** @var \Delorius\Page\Pagination\IPaginationRenderer */
    protected $renderer;
    /** @var array */
    protected $query = array();
    /** @var  string */
    protected $nameRoute;
    /** @var  string */
    protected $nameQuery = 'page';
    /** @var bool */
    protected $isItemsAll = false;

    /** @var int */
    private $base = 1;
    /** @var int */
    private $itemsPerPage = 1;
    /** @var int */
    private $page;
    /** @var int|NULL */
    private $itemCount;


    /** @return  \Delorius\Page\Pagination\PaginationBuilder */
    public static function factory($facts)
    {
        if ($facts instanceof ORM) {
            return new PaginationBuilder(new ORMHandlerPagination($facts));
        }

        throw new Error('Unknown data type');
    }

    protected function __construct(IHandlerPagination $handler)
    {
        $this->handler = $handler;
    }

    /**
     * Sets current page number.
     * @param  int
     * @return self
     */
    public function setPage($page, $name = 'page')
    {
        if ($page === self::ITEMS_ALL) {
            $this->isItemsAll = true;
            $this->page = 0;
        }

        $this->page = (int)$page;
        $this->nameQuery = $name;
        return $this;
    }


    /**
     * Returns current page number.
     * @return int
     */
    public function getPage()
    {
        return $this->base + $this->getPageIndex();
    }


    /**
     * Returns first page number.
     * @return int
     */
    public function getFirstPage()
    {
        return $this->base;
    }


    /**
     * Returns last page number.
     * @return int|NULL
     */
    public function getLastPage()
    {
        return $this->itemCount === NULL ? NULL : $this->base + max(0, $this->getPageCount() - 1);
    }


    /**
     * Sets first page (base) number.
     * @param  int
     * @return self
     */
    public function setBase($base)
    {
        $this->base = (int)$base;
        return $this;
    }


    /**
     * Returns first page (base) number.
     * @return int
     */
    public function getBase()
    {
        return $this->base;
    }


    /**
     * Returns zero-based page number.
     * @return int
     */
    protected function getPageIndex()
    {
        $index = max(0, $this->page - $this->base);
        return $this->itemCount === NULL ? $index : min($index, max(0, $this->getPageCount() - 1));
    }


    /**
     * Is the current page the first one?
     * @return bool
     */
    public function isFirst()
    {
        return $this->getPageIndex() === 0;
    }


    /**
     * Is the current page the last one?
     * @return bool
     */
    public function isLast()
    {
        return $this->itemCount === NULL ? FALSE : $this->getPageIndex() >= $this->getPageCount() - 1;
    }

    /**
     * @return bool
     */
    public function isPageAll()
    {
        return $this->isItemsAll;
    }

    /**
     * Returns the total number of pages.
     * @return int|NULL
     */
    public function getPageCount()
    {
        if ($this->isItemsAll) {
            return 0;
        }

        return $this->itemCount === NULL ? NULL : (int)ceil($this->itemCount / $this->itemsPerPage);
    }


    /**
     * Sets the number of items to display on a single page.
     * @param  int
     * @return self
     */
    public function setItemsPerPage($itemsPerPage)
    {
        $this->itemsPerPage = max(1, (int)$itemsPerPage);
        return $this;
    }


    /**
     * Returns the number of items to display on a single page.
     * @return int
     */
    public function getItemsPerPage()
    {
        return $this->itemsPerPage;
    }


    /**
     * Sets the total number of items.
     * @param  int (or NULL as infinity)
     * @return self
     */
    public function setItemCount($itemCount)
    {
        $this->itemCount = ($itemCount === FALSE || $itemCount === NULL) ? $this->handler->count() : max(0, (int)$itemCount);
        return $this;
    }


    /**
     * Returns the total number of items.
     * @return int|NULL
     */
    public function getItemCount()
    {
        return $this->itemCount;
    }


    /**
     * Returns the absolute index of the first item on current page.
     * @return int
     */
    public function getOffset()
    {
        return $this->getPageIndex() * $this->itemsPerPage;
    }


    /**
     * Returns the absolute index of the first item on current page in countdown paging.
     * @return int|NULL
     */
    public function getCountdownOffset()
    {
        return $this->itemCount === NULL
            ? NULL
            : max(0, $this->itemCount - ($this->getPageIndex() + 1) * $this->itemsPerPage);
    }


    /**
     * Returns the number of items on current page.
     * @return int|NULL
     */
    public function getLength()
    {
        return $this->itemCount === NULL
            ? $this->itemsPerPage
            : min($this->itemsPerPage, $this->itemCount - $this->getPageIndex() * $this->itemsPerPage);
    }

    /**
     * Возвращает количество невыведенных элементов
     * @return int|0
     */
    public function getRemainItems()
    {
        return $this->itemCount == NULL
            ? 0 :
            $this->itemCount - ($this->getPage() * $this->itemsPerPage);
    }

    /**
     * Возвращает количество позиций на следующей странице
     * @return int
     */
    public function getNextCountItems()
    {
        $remain = $this->getRemainItems();
        return $this->getRemainItems() < $this->itemsPerPage
            ? $remain
            : $this->itemsPerPage;
    }

    /**
     * Возвращает номер следующей страницы
     * @return int|false
     */
    public function getNextPage()
    {
        return $this->getPageCount() > $this->page
            ? $this->getPage() + 1
            : false;
    }


    /********************* url ***************************/

    /** @params string название роутера */
    public function setRoute($name)
    {
        $this->nameRoute = $name;
        return $this;
    }

    /** @params array дополнительные параметры для запросв */
    public function addQueries(array $query)
    {
        $query = Arrays::cleatOfNull($query);
        $this->query += $query;
        return $this;
    }

    /**
     * @param $page
     * @return array
     */
    public function getQuery($page)
    {
        if ((is_int($page) || $page == null) && ($page == 1 || $page == 0)) {
            unset($this->query[$this->nameQuery]);
        } else {
            $this->query[$this->nameQuery] = $page;
        }
        ksort($this->query, SORT_STRING);
        return (array)$this->query;
    }

    /** @params string указаный урл запускает */
    public function getUrlPage($page)
    {
        if ($this->nameRoute === FALSE || $this->nameRoute === NULL) {
            $q = http_build_query($this->getQuery($page));
            $url = '?' . $q;
        } else {
            $url = link_to($this->nameRoute, $this->getQuery($page));
        }
        return $url;
    }

    /**
     * Возращает урл на весь список товаров
     * @return string
     */
    public function getItemsAllUrlPage()
    {
        $query = $this->getQuery(self::ITEMS_ALL);
        if ($this->nameRoute === FALSE || $this->nameRoute === NULL) {
            $q = http_build_query($this->getQuery($query));
            $url = $q ? '?' . $q : '';
        } else {
            $url = link_to($this->nameRoute, $query);
        }
        return $url;
    }


    /********************* rendering ****************d*g**/

    /**
     * Sets form renderer.
     * @return $this  provides a fluent interface
     */
    public function setRenderer(IPaginationRenderer $renderer)
    {
        $this->renderer = $renderer;
        return $this;
    }

    /**
     * Returns form renderer.
     * @return IPaginationRenderer
     */
    final public function getRenderer()
    {
        if ($this->renderer === NULL) {
            $this->renderer = Environment::getContext()->getByType('Delorius\Page\Pagination\IPaginationRenderer');
        }
        return $this->renderer;
    }


    /** @return  HTML|string */
    public function render()
    {
        return $this->getRenderer()->render($this);
    }

    /**
     * @return Html|string
     */
    public function __toString()
    {
        return $this->render();
    }

    /********************* result working data ******************/


    public function result()
    {
        if (!$this->isPageAll()) {
            $this->handler->limit($this->getLength());
            $this->handler->offset($this->getOffset());
        }
        return $this->handler->result();
    }

    public function as_array()
    {
        return array(
            'name' => $this->nameQuery,
            'current' => $this->getPage(),
            'next' => $this->getNextPage(),
            'pre' => $this->getPage() - 1,
            'first' => $this->getFirstPage(),
            'last' => $this->getLastPage(),
            'is_first' => $this->isFirst() ? 1 : 0,
            'is_last' => $this->isLast() ? 1 : 0,
            'is_all' => $this->isPageAll() ? 1 : 0,
            'total' => $this->getPageCount(),
            'query' => (array)$this->getQuery($this->getPage() + 1)
        );
    }
}


