<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Catalog\Entity\Category;

class VideoCategorySitemaps extends CategorySitemaps
{
    protected $router = 'video_category';
    protected $typeId = Category::TYPE_VIDEO;
    protected $typeName = 'video';
}