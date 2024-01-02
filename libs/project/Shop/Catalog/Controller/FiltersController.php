<?php

namespace Shop\Catalog\Controller;

use CMS\Core\Helper\Helpers;
use Delorius\Application\UI\Controller;
use Delorius\DataBase\DB;
use Delorius\Utils\Arrays;
use Shop\Catalog\Entity\Category;
use Shop\Catalog\Entity\CategoryMulti;
use Shop\Catalog\Entity\Collection;
use Shop\Catalog\Entity\Filter;
use Shop\Commodity\Entity\Characteristics;
use Shop\Commodity\Entity\CharacteristicsGoods;
use Shop\Commodity\Entity\CharacteristicsValues;
use Shop\Commodity\Entity\Goods;
use Shop\Commodity\Entity\Unit;
use Shop\Commodity\Entity\Vendor;

class FiltersController extends Controller
{
    /**
     * @var \Shop\Store\Model\CurrencyBuilder
     * @service currency
     * @inject
     */
    public $currency;

    /** @var \Shop\Catalog\Entity\Category */
    protected static $category;

    /** @var \Shop\Catalog\Entity\Collection */
    protected static $collection;

    public function listPartial()
    {
        if (!$this->hasSite('category') && !$this->hasSite('collectionCategory')) {
            return null;
        }
        $var['category'] = self::$category = $this->getSite('category');

        if ($this->hasSite('collectionCategory')) {
            self::$collection = $this->getSite('collectionCategory');
            $filters = self::$collection->getFilters();
            $var['filters'] = array();
            foreach ($filters as $item) {
                $var['filters'][] = $item;
            }
            if (self::$collection->cid) {
                $filter_collections = Filter::model()
                    ->where('target_id', '=', self::$collection->cid)
                    ->where('target_type', '=', Helpers::getTableId(self::$category))
                    ->where_open()
                    ->or_where('type_id', '=', Filter::TYPE_COLLECTION)
                    ->or_where('type_id', '=', Filter::TYPE_COLLECTION_MAIN)
                    ->where_close()
                    ->find_all();

                foreach ($filter_collections as $item) {
                    if ($item->type_id == Filter::TYPE_COLLECTION) {
                        $var['filters'][] = $item;
                    }
                    if ($item->type_id == Filter::TYPE_COLLECTION_MAIN) {
                        array_unshift($var['filters'], $item);
                    }
                }
            }

        } else {
            $var['filters'] = $filters = self::$category->getFilters();
        }

        if (!count($var['filters'])) {
            $this->response($this->view->load('shop/filters/none', $var));
            return null;
        }

        if (!$this->hasSite('categoryFilter')) {
            $url = $this->container->getService('url');
            $url->setQuery(array());
            $var['url'] = $url;
        } else {
            $link = $this->getSite('categoryLink');
            $var['url'] = $link;
        }


        $var['get'] = $this->getParamsFilter();
        $this->response($this->view->load('shop/filters/index', $var));
    }

    public function categoryPartial(Filter $filter)
    {
        $var['get'] = $get = $this->getParamsFilter();
        $var['categories'] = array();
        if (self::$category->children) {
            $categories = Category::model()->select()->sort()->active()->cached();
            $categories->where('pid', '=', self::$category->pk());
            $var['categories'] = $categories->find_all();
        }
        $var['filter'] = $filter;
        $this->response($this->view->load('shop/filters/_categories', $var));
    }

    public function goodsParamsPartial(Filter $filter)
    {
        $var['get'] = $get = $this->getParamsFilter();

        if ($filter->value == 'price') { //price

            $goods_table = new Goods();
            /** price */
            $execute = DB::select(array(DB::expr('MAX(value_system)'), 'max'), array(DB::expr('MIN(value_system)'), 'min'))
                ->where('ctype', '=', $this->getSite('goodsTypeId'))
                ->where('status', '=', 1)
                ->from($goods_table->table_name());

            if (self::$collection) {
                $ids = $this->getCollectionCategoryGoodsId();
                if (count($ids)) {
                    $execute->where('goods_id', 'in', $ids);
                }
            }

            $ids = $this->getSite('childrenCatIds');
            $ids[] = self::$category->pk();

            if (!$goods_table->isMulti()) {
                $execute->where('cid', 'in', $ids);
            } else {
                $table_multi = CategoryMulti::model();
                $execute->join($table_multi->table_name(), 'inner')
                    ->on($table_multi->table_name() . '.product_id', '=', $goods_table->table_name() . '.' . $goods_table->primary_key())
                    ->where($table_multi->table_name() . '.cid', 'in', $ids);
            }

            $result = $execute->execute($goods_table->db_config());
            $var['max'] = $this->currency->convert($result->get('max'), SYSTEM_CURRENCY);
            $var['min'] = $this->currency->convert($result->get('min'), SYSTEM_CURRENCY);
            $var['price_max'] = $this->currency->convert(isset($get['price_max']) ? (int)$get['price_max'] : $var['max'], SYSTEM_CURRENCY);
            $var['price_min'] = $this->currency->convert(isset($get['price_min']) ? (int)$get['price_min'] : $var['min'], SYSTEM_CURRENCY);
            $template = '_goods_price';

        } elseif ($filter->value == 'vendor_id') { //vendor_id
            $goods_table = new Goods();
            /** vendor */
            $vendors = DB::select('vendor_id', array(DB::expr('COUNT(goods_id)'), 'count'))
                ->from($goods_table->table_name())
                ->group_by('vendor_id')
                ->where('vendor_id', '>', 0);

            if (self::$collection) {
                $ids = $this->getCollectionCategoryGoodsId();
                if (count($ids)) {
                    $vendors->where('goods_id', 'in', $ids);
                }
            }

            $ids = $this->getSite('childrenCatIds');
            $ids[] = self::$category->pk();

            if (!$goods_table->isMulti()) {
                $vendors->where($goods_table->table_name() . '.cid', 'in', $ids);
            } else {
                $table_multi = CategoryMulti::model();
                $vendors->join($table_multi->table_name(), 'inner')
                    ->on($table_multi->table_name() . '.product_id', '=', $goods_table->table_name() . '.' . $goods_table->primary_key())
                    ->where($table_multi->table_name() . '.cid', 'in', $ids);
            }

            $result = $vendors->execute($goods_table->db_config());
            $idsVendors = $arrVendors = array();
            foreach ($result as $item) {
                $idsVendors[] = $item['vendor_id'];
                $arrVendors[$item['vendor_id']] = $item;
            }

            $var['vendors'] = array();
            if (sizeof($idsVendors)) {
                $ormVendor = Vendor::model()
                    ->select('vendor_id', 'name')
                    ->where('vendor_id', 'IN', $idsVendors)
                    ->sort()
                    ->find_all();
                foreach ($ormVendor as $item) {
                    $var['vendors'][] = array(
                        'vendor_id' => $item['vendor_id'],
                        'name' => $item['name'],
                        'count' => $arrVendors[$item['vendor_id']]['count']
                    );
                }
            }
            $template = '_goods_vendor';
        } else {
            return null;
        }

        $var['filter'] = $filter;
        $this->response($this->view->load('shop/filters/' . $template, $var));
    }

    public function featurePartial(Filter $filter)
    {
        $characterId = (int)$filter->value;
        $var['get'] = $get = $this->getParamsFilter();
        $url = $this->container->getService('url');
        $url->setQuery(array());
        $var['url'] = $url;
        $feature = new Characteristics($characterId);
        if (!$filter->loaded()) {
            return null;
        }

        switch ($feature->filter) {
            case Characteristics::FILTER_SELECT:
                $template = '_feature_select';
                break;
            case Characteristics::FILTER_LINK:
                $template = '_feature_link';
                break;
            case Characteristics::FILTER_RADIO:
                $template = '_feature_radio';
                break;
            case Characteristics::FILTER_COLOR:
                $template = '_feature_color';
                break;
            case Characteristics::FILTER_OTHER:
                $template = '_feature_' . $feature->filter_other;
                break;
            case Characteristics::FILTER_CHECKBOX:
            case Characteristics::FILTER_SLIDER:
            default:
                $template = '_feature_checkbox';
                break;
        }


        $goods_table = new Goods();
        $chara_goods = new CharacteristicsGoods();
        $chara_goods->join($goods_table->table_name(), 'INNER')
            ->on($chara_goods->table_name() . '.target_id', '=', $goods_table->table_name() . '.goods_id')
            ->where($chara_goods->table_name() . '.target_type', '=', Helpers::getTableId($goods_table))
            ->where($chara_goods->table_name() . '.character_id', '=', $feature->pk())
            ->where($goods_table->table_name() . '.status', '=', 1)
            ->where($goods_table->table_name() . '.ctype', '=', $this->getSite('goodsTypeId'));


        $ids = $this->getSite('childrenCatIds');
        $ids[] = self::$category->pk();

        if (!$goods_table->isMulti()) {
            $chara_goods->where($goods_table->table_name() . '.cid', 'in', $ids);
        } elseif (self::$category) {
            $table_multi = CategoryMulti::model();
            $chara_goods->join($table_multi->table_name(), 'inner')
                ->on($table_multi->table_name() . '.product_id', '=', $goods_table->table_name() . '.' . $goods_table->primary_key())
                ->where($table_multi->table_name() . '.cid', 'in', $ids);
        }

        if (self::$collection) {
            $ids = $this->getCollectionCategoryGoodsId();
            if (count($ids)) {
                $chara_goods->where($goods_table->table_name() . '.goods_id', 'in', $ids);
            }
        }

        $result = $chara_goods->find_all();

        $ids = array();
        foreach ($result as $item) {
            $ids[] = $item->value_id;
        }

        $var['values'] = array();
        if (count($ids)) {
            $values = CharacteristicsValues::model()
                ->select()
                ->where('value_id', 'IN', $ids)
                ->sort()
                ->find_all();
            $var['values'] = $values;
        }
        $units = Unit::model()->select()->cached()->find_all();
        $var['units'] = Arrays::resultAsArrayKey($units, 'unit_id');
        $var['filter'] = $filter;
        $var['feature'] = $feature;
        $this->response($this->view->load('shop/filters/' . $template, $var));
    }

    public function collectionMainPartial(Filter $filter)
    {
        $collections = Collection::model()
            ->select('name', 'url', 'id')
            ->where('type', '=', 1)
            ->where('cid', '=', self::$category->pk())
            ->sort()
            ->active()
            ->find_all();
        $var['collections'] = $collections;
        $var['filter'] = $filter;
        $this->response($this->view->load('shop/filters/_collections_main', $var));
    }

    public function collectionPartial(Filter $filter)
    {
        $collections = Collection::model()
            ->select('name', 'url', 'id')
            ->where('type', '=', 0)
            ->where('cid', '=', self::$category->pk())
            ->sort()
            ->active()
            ->find_all();
        $var['collections'] = $collections;
        $var['filter'] = $filter;
        $this->response($this->view->load('shop/filters/_collections', $var));
    }

    /**
     * @return array
     */
    protected function getCollectionCategoryGoodsId()
    {
        $filters = self::$collection->mergeRequest();
        $ids = \Shop\Catalog\Helpers\Filter::getGoodsIds($filters);
        return $ids;
    }

    /**
     * @return array
     */
    protected function getParamsFilter()
    {
        $params = array();
        if ($this->hasSite('categoryFilter')) {
            $filter = $this->getSite('categoryFilter');
            $params = \Shop\Catalog\Helpers\Filter::parser_hash($filter->hash);
        } else {
            $get = $this->httpRequest->getRequest();
            $params = \Shop\Catalog\Helpers\Filter::parser_request($get);
        }

        return $params;
    }

}