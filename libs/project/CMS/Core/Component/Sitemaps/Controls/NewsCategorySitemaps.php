<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Catalog\Entity\Category;

class NewsCategorySitemaps extends CategorySitemaps
{
    protected $router = 'news_category';
    protected $typeId = Category::TYPE_NEWS;
    protected $typeName = 'news';
}