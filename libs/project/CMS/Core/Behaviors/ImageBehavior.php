<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Image;
use CMS\Core\Helper\Helpers;
use CMS\Core\Helper\ImageHelper;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;
use Delorius\Http\FileUpload;

class ImageBehavior extends ORMBehavior
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


    /** @return \CMS\Core\Entity\Image */
    public function getImage()
    {
        $orm = $this->getOwner();
        if (!isset(self::$cached[$orm->table_name()][$orm->pk()])) {
            self::$cached[$orm->table_name()][$orm->pk()] =
                Image::model()
                    ->whereByTargetId($orm->pk())
                    ->whereByTargetType($orm)
                    ->find();
        }
        return self::$cached[$orm->table_name()][$orm->pk()];
    }


    /** @return array|bool */
    public function setImage(FileUpload $file)
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

        $oldImage = $this->getImage();
        if ($oldImage->loaded()) {
            $oldImage->delete();
        }

        try {
            $image = new Image();
            $image->values($result);
            $image->target_id = $this->getOwner()->pk();
            $image->target_type = Helpers::getTableId($this->getOwner());
            $image->save(true);
            return $image->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    public function setImagePath($path)
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

        $oldImage = $this->getImage();
        if ($oldImage->loaded()) {
            $oldImage->delete();
        }

        try {
            $image = new Image();
            $image->values($result);
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
        $image = $this->getImage();
        unset(self::$cached[$image->target_type][$image->target_id]);
        if ($image->loaded()) {
            $image->delete();
        }


    }


}