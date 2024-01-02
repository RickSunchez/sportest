<?php
namespace CMS\Core\Component\Snippet\Controls;

use CMS\Core\Component\Snippet\AParserRenderer;
use CMS\Core\Entity\Image;

class ImageTag extends AParserRenderer
{
    private static $_images = array();

    public function render()
    {
        if (!isset(self::$_images[$this->path])) {
            $image = new Image($this->path);
            if (!$image->loaded()) {
                $this->error(_sf('Картинка c ID:{0} не найдена', $this->path));
                self::$_images[$this->path] = false;
            } else {
                self::$_images[$this->path] = $image;
            }
        }

        if(!($image = self::$_images[$this->path])){
            return '/source/images/no.png';
        }

        if (array_key_exists('thumb', $this->query)) {
            return $image->preview;
        } else {
            return $image->normal;
        }
    }

}