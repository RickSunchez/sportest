<?php

$admin = array(
    'goods_category_types' => array(\Shop\Catalog\Entity\Category::TYPE_GOODS),
    'type' => array(
        array(
            'id' => \Shop\Catalog\Entity\Category::TYPE_GOODS,
            'name' => 'Продукция',
            'path' => array('admin_goods'=>array('action'=>'list')),
            'add' => array('admin_goods'=>array('action'=>'add')),
            'addName' => 'Товар',
        ),
    ),

);

return array(
    'shop' => array(
        'admin' => $admin
    )
);