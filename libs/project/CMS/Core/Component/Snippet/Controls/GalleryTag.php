<?php
namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use CMS\Core\Entity\Gallery;
use Delorius\View\View;

class GalleryTag extends AParserRenderer
{
    public function render()
    {
        $galleryId = $this->path;
        $gallery = Gallery::model($galleryId);
        if (!$gallery->loaded()) {
            return '';
        }
        $view = new View();
        $var['gallery'] = $gallery;
        $images = $gallery->getImages();
        $var['images'] = $images;
        $theme = $this->query['theme'] ? '_' . $this->query['theme'] : '';
        return $view->load('cms/gallery/_list' . $theme, $var);
    }

}