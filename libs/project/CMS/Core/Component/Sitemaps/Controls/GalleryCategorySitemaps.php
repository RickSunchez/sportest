<?php
namespace CMS\Core\Component\Sitemaps\Controls;

use CMS\Catalog\Entity\Category;

class GalleryCategorySitemaps extends CategorySitemaps
{
    protected $router = 'gallery_category';
    protected $typeId = Category::TYPE_GALLERY;
    protected $typeName = 'gallery';
}