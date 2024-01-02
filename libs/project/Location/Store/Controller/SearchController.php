<?php
namespace Location\Store\Controller;

use Delorius\View\Html;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Goods;

class SearchController extends \Shop\Catalog\Controller\ShopController
{

    /**
     * @Post
     * @throws \Delorius\Exception\Error
     */
    public function resultAction()
    {
        $term = $this->httpRequest->getPost('term');
        $term = Html::clearTags($term);
        $items = array();

        if (!$term) {
            return;
        }

        $categories = Category::model()
            ->type($this->type_id)
            ->select('name', 'cid', 'url')
            ->active()
            ->sort()
            ->where('name', 'like', '%' . $term . '%')
            ->find_all();
        foreach ($categories as $item) {
            $item['link'] = link_to_city($this->router, array('cid' => $item['cid'], 'url' => $item['url']));
            unset($item['cid'], $item['url']);
            $item['type'] = 'category';
            $items[] = $item;
        }

        $goods = Goods::model()
            ->limit(10)
            ->active()
            ->ctype($this->type_id)
            ->is_amount()
            ->sort()
            ->where('name', 'like', '%' . $term . '%')
            ->find_all();
        foreach ($goods as $item) {
            $arr['link'] = $item->link();
            $arr['name'] = $item->name;
            $arr['value'] = $item->value;
            $arr['price'] = $item->getPrice();
            $arr['id'] = $item->pk();
            $arr['type'] = 'product';
            $items[] = $arr;
        }

        $this->response($items);
    }

}