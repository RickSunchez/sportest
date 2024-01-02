<?php
define('DIR_INDEX', __DIR__ . '/../');
include_once DIR_INDEX . '/libs/bootstrap.php';

ignore_user_abort(1);
set_time_limit(0);
ini_set('memory_limit', '-1');

$categories = \Shop\Catalog\Entity\Category::model()->select()->sort()->find_all();

$cats = array();
foreach ($categories as $category) {
    $cats[$category['pid']][] = $category;
}


Class CategoryCharaCSV
{

    protected $cats = array();
    protected $products = array();
    /** @var \Delorius\Utils\CSV\CSVWrite */
    protected $csv = null;
    protected $charas_values = array();


    public function __construct($cats)
    {
        $this->cats = $cats;
        $this->charas_values = $this->getCharasValue();
        $this->csv = new \Delorius\Utils\CSV\CSVWrite();
        $this->csv->fields = array(
            'lvl',
            'cid',
            'all',
            'character_id',
            'value_id',
            'type',
            'name',
            'pos',
            'count_chara',
            'count_value',
            'count_goods',
            'count_sub',
            'filter_status',
            'filter_id',
            'filter_name',
            'filter_pos',
        );
    }

    #chara start


    public function init($pid = 0, $lvl = 0)
    {

        $lvl++;
        foreach ($this->cats[$pid] as $cat) {

            $category = \Shop\Catalog\Entity\Category::mock($cat);
            $rows = array();
            
            $rows[] = array(
                'lvl' => $lvl,
                'cid' => $cat['cid'],
                'all' => $cat['show_cats'],
                'type' => 'category',
                'name' => $cat['name'],
                'pos' => $cat['pos'],
                'count_goods' => $cat['goods'],
                'count_sub' => $cat['children'],
                'filter_status'=>count($category->getFilters())
            );

            if (!$cat['show_cats']) {

                $filters = $this->getFiltersIds($category);
                $category_charas = $this->getCharasIds($category);

                foreach ($category_charas as $character_id => $values) {

                    $filter = $filters[\Shop\Catalog\Entity\Filter::TYPE_FEATURE][$character_id];

                    $ch = $this->charas_values[$character_id]['object'];
                    $character_id = $ch['character_id'];
                    $row_chara = array(
                        'cid' => $cat['cid'],
                        'character_id' => $character_id,
                        'type' => 'character',
                        'name' => $ch['name'],
                        'filter_status' => $filter ? 1 : 0,
                        'filter_id' => $filter['filter_id'],
                        'filter_name' => $filter['name'],
                        'filter_pos' => $filter['pos'],
                    );


                    #value start
                    $count_value = 0;
                    $count_value_goods = 0;
                    if (count($values)) {
                        $row_values = array();
                        foreach ($values as $value_id => $count) {
                            $value = $this->charas_values[$character_id]['values'][$value_id];
                            $row_value = array(
                                'cid' => $cat['cid'],
                                'character_id' => $character_id,
                                'value_id' => $value['value_id'],
                                'type' => 'value',
                                'name' => $value['name'],
                                'count_goods' => $count,
                            );
                            $row_values[] = $row_value;
                            $count_value++;
                            $count_value_goods += $count;
                        }
                    }
                    #value end

                    $row_chara['count_value'] = $count_value;
                    $row_chara['count_goods'] = $count_value_goods;
                    $rows[] = $row_chara;

                    foreach ($row_values as $value) {
                        $rows[] = $value;
                    }
                    $rows[0]['count_chara']++;
                    $rows[0]['count_value'] += $count_value;
                    $count_value = 0;
                }
                #chara end
            }

            $this->csv->addRows($rows);

            if (count($this->cats[$cat['cid']])) {
                $this->init($cat['cid'], $lvl);
            }


//            break;
        }

    }

    public function save($name)
    {
        $this->csv->save($name);
    }


    protected function getGoodsIds(\Shop\Catalog\Entity\Category $category)
    {
        $res = $category->getChildren();
        if (count($res)) {
            foreach ($res as $cat) {
                $idsCat[] = $cat['cid'];
            }
        }
        $idsCat[] = $category->pk();

        $goods = \Shop\Commodity\Entity\Goods::model()
            ->where('cid', 'in', $idsCat)
            ->select('goods_id')
            ->find_all();
        $idsGoods = array();
        foreach ($goods as $item) {
            $idsGoods[] = $item['goods_id'];
        }
        return $idsGoods;
    }


    protected function getCharasIds(\Shop\Catalog\Entity\Category $category)
    {
        $idsGoods = $this->getGoodsIds($category);

        $category_charas = array();
        if (count($idsGoods)) {
            $charas = \Shop\Commodity\Entity\CharacteristicsGoods::model()
                ->select()
                ->where('target_type', '=', CMS\Core\Helper\Helpers::getTableId(\Shop\Commodity\Entity\Goods::model()))
                ->where('target_id', 'IN', $idsGoods)
                ->find_all();

            foreach ($charas as $item) {
                $category_charas[$item['character_id']][$item['value_id']] += 1;
            }
        }

        return $category_charas;
    }


    protected function getFiltersIds(\Shop\Catalog\Entity\Category $category)
    {
        $filters = \Shop\Catalog\Entity\Filter::model()
            ->select()
            ->where('target_type', '=', CMS\Core\Helper\Helpers::getTableId($category))
            ->where('target_id', '=', $category->pk())
            ->find_all();

        $arr = array();
        foreach ($filters as $item) {
            $arr[$item['type_id']][$item['value']] = $item;
        }
        return $arr;
    }


    protected function getCharasValue()
    {
        $charas = \Shop\Commodity\Entity\Characteristics::model()->select()->find_all();
        $arr = array();
        foreach ($charas as $item) {
            $arr[$item['character_id']]['object'] = $item;
        }

        $units = \Delorius\Utils\Arrays::resultAsArrayKey(
            \Shop\Commodity\Entity\Unit::model()->select()->find_all(),
            'unit_id'
        );


        $values = \Shop\Commodity\Entity\CharacteristicsValues::model()->select()->find_all();
        foreach ($values as $value) {
            $value['unit'] = $units[$value['unit_id']]['abbr'];
            $arr[$value['character_id']]['values'][$value['value_id']] = $value;
        }

        return $arr;
    }
}

$cvs = new \CategoryCharaCSV($cats);
$cvs->init();
$cvs->save('cats.csv');



