<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Image;
use CMS\Core\Helper\Helpers;
use CMS\Core\Helper\ImageHelper;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;

class GalleryBehavior extends ORMBehavior
{

    /** @var string */
    public $path = 'gallery';
    public $normal_width = IMAGE_WIDTH;
    public $normal_height = IMAGE_HEIGHT;
    public $preview_width = IMAGE_PREVIEW_WIDTH;
    public $preview_height = IMAGE_PREVIEW_HEIGHT;
    public $crop = false;
    public $ratio_fill = false;
    public $background_color = false;
    public $watermark = false;
    public $watermark_type = false;
    public $watermark_preview = false;
    public $watermark_preview_type = false;

    protected static $cached = array();

    /**
     * @return ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    public function getImages()
    {
        /** @var ORM $orm */
        $orm = $this->getOwner();
        if (!isset(self::$cached[$orm->table_name()][$orm->pk()])) {
            self::$cached[$orm->table_name()][$orm->pk()] = Image::model()
                ->whereByTargetId($orm->pk())
                ->whereByTargetType($orm)
                ->sort()
                ->find_all();
        }
        return self::$cached[$orm->table_name()][$orm->pk()];
    }

    /**
     * @return ORM
     * @throws \Delorius\Exception\Error
     */
    public function getMainImage()
    {
        /** @var ORM $orm */
        $orm = $this->getOwner();
        if (!isset(self::$cached['main'][$orm->table_name()][$orm->pk()])) {

            $image = Image::model()
                ->whereByTargetId($orm->pk())
                ->whereByTargetType($orm)
                ->main(true)
                ->find();

            self::$cached['main'][$orm->table_name()][$orm->pk()] = $image;

        }
        return self::$cached['main'][$orm->table_name()][$orm->pk()];
    }

    /**
     * @param int $limit
     * @param $offset
     * @return ORM|\Delorius\DataBase\Result
     * @throws \Delorius\Exception\Error
     */
    public function offsetImage($limit = 1, $offset = 1)
    {
        /** @var ORM $orm */
        $orm = $this->getOwner();
        $i = Image::model()
            ->whereByTargetId($orm->pk())
            ->whereByTargetType($orm)
            ->sort()
            ->offset($offset);
        if ($limit == 1) {
            return $i->find();
        } else {
            return $i->find_all();
        }
    }


    /** @return array|bool */
    public function addImage($file)
    {
        if (!$this->getOwner()->loaded()) {
            return false;
        }

        $result = ImageHelper::download(
            $file,
            $this->path,
            $this->normal_width,
            $this->normal_height,
            $this->preview_width,
            $this->preview_height,
            $this->crop,
            $this->ratio_fill,
            $this->background_color,
            $this->watermark,
            $this->watermark_type,
            $this->watermark_preview,
            $this->watermark_preview_type
        );
        if (!$result) {
            return false;
        }

        try {
            $image = new Image();
            $image->values($result, array('horizontal', 'width', 'height', 'pre_width', 'pre_height', 'normal', 'preview'));
            $image->target_id = $this->getOwner()->pk();
            $image->target_type = Helpers::getTableId($this->getOwner());
            $image->save(true);
            return $image->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /** @return array|bool */
    public function addImagePath($path)
    {
        if (!$this->getOwner()->loaded()) {
            return false;
        }


        $result = ImageHelper::downloadByPath(
            $path,
            $this->path,
            $this->normal_width,
            $this->normal_height,
            $this->preview_width,
            $this->preview_height,
            $this->crop,
            $this->ratio_fill,
            $this->background_color,
            $this->watermark,
            $this->watermark_type,
            $this->watermark_preview,
            $this->watermark_preview_type
        );
        if (!$result) {
            return false;
        }

        try {
            $image = new Image();
            $image->values($result, array('horizontal', 'width', 'height', 'pre_width', 'pre_height', 'normal', 'preview'));
            $image->target_id = $this->getOwner()->pk();
            $image->target_type = Helpers::getTableId($this->getOwner());
            $image->save(true);
            return $image->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    public function afterDelete(ORM $orm)
    {
        $images = $this->getImages();
        foreach ($images as $image) {
            $image->delete();
        }
        if ($images->count())
            $image->cache_delete();

        self::$cached = array();
    }


}