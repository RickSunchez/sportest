<?php
namespace CMS\Admin\Controller;

use CMS\SEO\Entity\Search;
use Delorius\Application\UI\Controller;
use Delorius\Page\Pagination\PaginationBuilder;

/**
 * @Template(name=admin)
 * @Admin
 * @SetTitle Поиск #admin_search?action=list
 */
class SearchSEOController extends Controller
{


    /** @AddTitle Список */
    public function listAction($page, $name = null, $type = null)
    {
        $var['get'] = $get = $this->httpRequest->getQuery();
        $search = Search::model()->sort();
        if ($name) {
            $search->where('query_str', 'like', '%' . $name . '%');
        }
        if ($type) {
            $search->where('type', '=', $type);
        }
        $pagination = PaginationBuilder::factory($search)
            ->setItemCount(false)
            ->setPage($page)
            ->setItemsPerPage(ADMIN_PER_PAGE * 4)
            ->addQueries($get);

        $result = $pagination->result();
        $var['pagination'] = $pagination;
        $var['search'] = array();
        foreach ($result as $item) {
            $var['search'][] = $item->as_array();
        }
        $this->response($this->view->load('cms/seo/search/list', $var));
    }

}