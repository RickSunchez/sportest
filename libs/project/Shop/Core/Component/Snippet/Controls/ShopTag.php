<?php

namespace Shop\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use Delorius\Core\Environment;
use Delorius\DataBase\DB;
use Shop\Catalog\Entity\Category;
use Shop\Commodity\Entity\Goods;


class ShopTag extends AParserRenderer
{
    public function render()
    {

        if ($this->path == 'min_price') {
            if (isset($this->query['id'])) {
                $id = $this->query['id'];
            } else {
                $id = Environment::getContext()->getService('site')->categoryId;
            }

            $category = new Category($id);
            if (!$category->loaded()) {
                $this->error(_sf('Категория с ID:{0} не найдено', $id));
                return '';
            }

            $res = $category->getChildren();
            $idsCat = array();
            if (count($res)) {
                foreach ($res as $cat) {
                    $idsCat[] = $cat['cid'];
                }
            }
            $idsCat[] = $category->pk();

            $orm = Goods::model();

            $result = DB::select('value', 'code')
                ->from($orm->table_name())
                ->where('cid', 'in', $idsCat)
                ->where('status', '=', 1)
                ->order_by('value_system', 'ASC')
                ->limit(1)
                ->execute($orm->db_config());

            $product = Goods::mock(
                array(
                    'value' => $result->get('value'),
                    'code' => $result->get('code'),
                )
            );

            return $product->getPrice();

        }

        if ($this->path == 'category') {
            $id = $this->query['id'];
            $category = new Category($id);
            if (!$category->loaded()) {
                $this->error(_sf('Категория с ID:{0} не найдено', $id));
                return '';
            }
            return $this->getLinkCategory($category);
        }

        if ($this->path == 'product') {
            $id = $this->query['id'];
            $product = new Goods($id);
            if (!$product->loaded()) {
                $this->error(_sf('Товар с ID:{0} не найден', $id));
                return '';
            }
            return $product->link();
        }

        return '';

    }

    protected function getConfigShop($type_id)
    {
        return Environment::getContext()->getParameters('shop.shop.type.' . $type_id);
    }

    protected function getLinkCategory(Category $category)
    {
        $config = $this->getConfigShop($category->type_id);
        return link_to($config['router'], array('cid' => $category->pk(), 'url' => $category->url));
    }


}