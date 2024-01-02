<?php
define('DIR_INDEX', __DIR__ . '/../');
include_once DIR_INDEX . '/libs/bootstrap.php';

ignore_user_abort(1);
set_time_limit(0);
ini_set('memory_limit', '-1');

$not = array('Назначение');
$set = array('Бренд/Марка' => 'Производитель');

$csv = new \Delorius\Utils\CSV\CSVLoader('cats.csv');
$duble = array();
$i = $c = 0;

foreach ($csv->getItems() as $key => $data) {

    foreach($data as $k=>$v){
        $k = \Delorius\Utils\Strings::fixEncoding($k);
        $v = \Delorius\Utils\Strings::fixEncoding($v);
        $data[$k] = $v;
    }

    if ($data['type'] == 'category') {

        if ($data['all'] == 0 && ($data['filter_status'] >= 1 OR $data['count_sub'] >= 1)) {

            $category = new \Shop\Catalog\Entity\Category($data['cid']);
            if ($category->loaded()) {

                #price
                $filter = \Shop\Catalog\Entity\Filter::model()
                    ->where('target_id', '=', $category->pk())
                    ->where('target_type', '=', \CMS\Core\Helper\Helpers::getTableId($category))
                    ->where('type_id', '=', \Shop\Catalog\Entity\Filter::TYPE_GOODS)
                    ->where('value', '=', 'price')
                    ->find();

                if (!$filter->loaded()) {
                    $category->addFilter(array(
                        'name' => 'Цена',
                        'type_id' => \Shop\Catalog\Entity\Filter::TYPE_GOODS,
                        'value' => 'price',
                        'pos' => 50
                    ));
                }else{
                    $filter->pos = 50;
                    $filter->save();
                }
                #end price

                if ($data['count_sub'] && false) {#subcategory
                    $filter = \Shop\Catalog\Entity\Filter::model()
                        ->where('target_id', '=', $category->pk())
                        ->where('target_type', '=', \CMS\Core\Helper\Helpers::getTableId($category))
                        ->where('type_id', '=', \Shop\Catalog\Entity\Filter::TYPE_CATEGORY)
                        ->find();

                    if (!$filter->loaded()) {
                        $category->addFilter(array(
                            'name' => 'Подкатегории',
                            'type_id' => \Shop\Catalog\Entity\Filter::TYPE_CATEGORY,
                            'value' => 0,
                            'pos' => 40
                        ));
                    }
                }
                #end subcategory
            }
        }


    }

    if ($data['type'] == 'character' && $data['filter_status'] == 1){


        echo _sf('cid={0}: {1} - {2} - {3} <br/>',$data['cid'],$data['type'],$data['name'],$data['count_value']);

        $category = new \Shop\Catalog\Entity\Category($data['cid']);
        if ($category->loaded()) {
            echo "category ok <br/>";


            $filter = \Shop\Catalog\Entity\Filter::model()
                ->where('target_id', '=', $category->pk())
                ->where('target_type', '=', \CMS\Core\Helper\Helpers::getTableId($category))
                ->where('name', '=', \Delorius\Utils\Strings::firstUpper($data['name']))
                ->where('type_id', '=', \Shop\Catalog\Entity\Filter::TYPE_FEATURE)
                ->where('value', '=', $data['character_id'])
                ->find();

            if (!$filter->loaded()) {
                echo "filter ok <br/>";
                $category->addFilter(array(
                    'name' => \Delorius\Utils\Strings::firstUpper($data['name']),
                    'type_id' => \Shop\Catalog\Entity\Filter::TYPE_FEATURE,
                    'value' => $data['character_id'],
                    'pos' => 30
                ));
            }
        }


        $i++;
    }
}


#not name - Назначение , Марка замка

#set name - Бренд/Марка => Производитель

var_dump('finish = ' . $i);



