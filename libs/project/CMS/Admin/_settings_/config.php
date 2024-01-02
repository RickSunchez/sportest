<?php
defined('DELORIUS') or die('access denied');

return array(
    'category_type' => array(
        array(
            'id' => \CMS\Catalog\Entity\Category::TYPE_NEWS,
            'name' => 'Новости',
            'path' => link_to('admin_news', array('action' => 'list')),
            'add' => link_to('admin_news', array('action' => 'add')),
            'addName' => 'Новость',
//            'param' => 'cid',
//            'update'=> link by update
        ),
        array(
            'id' => \CMS\Catalog\Entity\Category::TYPE_GALLERY,
            'name' => 'Галерии',
            'path' => link_to('admin_gallery', array('action' => 'list')),
            'add' => link_to('admin_gallery', array('action' => 'list')),
            'addName' => 'Галерею',
        ),
        array(
            'id' => \CMS\Catalog\Entity\Category::TYPE_DOCS,
            'name' => 'Документы',
            'path' => link_to('admin_doc', array('action' => 'list')),
            'add' => link_to('admin_doc', array('action' => 'list')),
            'addName' => 'Документ',
        ),
        array(
            'id' => \CMS\Catalog\Entity\Category::TYPE_ARTICLE,
            'name' => 'Статьи',
            'path' => link_to('admin_article', array('action' => 'list')),
            'add' => link_to('admin_article', array('action' => 'add')),
            'addName' => 'Статью',
        ),
    ),
);