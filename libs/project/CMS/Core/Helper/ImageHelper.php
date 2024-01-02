<?php
namespace CMS\Core\Helper;

use Delorius\Core\Environment;
use Delorius\Http\FileUpload;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Path;
use Delorius\Utils\Strings;

class ImageHelper
{
    /**
     * Возращает массив данных по разруженой картинке или false
     * @return array('horizontal','width','height','normal','preview')|false
     */
    public static function download(
        FileUpload $file,
        $dir = 'files',
        $normal_width = IMAGE_WIDTH,
        $normal_height = IMAGE_HEIGHT,
        $preview_width = IMAGE_PREVIEW_WIDTH,
        $preview_height = IMAGE_PREVIEW_HEIGHT,
        $crop = false,
        $ratio_fill = false,
        $background_color = false,
        $watermark = false,
        $watermark_type = false,
        $watermark_preview = false,
        $watermark_preview_type = false,
        $old_normal = null
    )
    {
        $result = array(
            'horizontal' => '',
            'width' => '',
            'height' => '',
            'normal' => '',
            'preview' => ''
        );
        if (!$file->isImage())
            return false;

        $image = new \Upload($file);
        if (!$image->uploaded) {
            return false;
        }

        /*
         * путь до место хранения картинки
         */
        $basePathUpload = Environment::getContext()->getParameters('path.upload');
        $profilePath = '/' . $dir . '/' . date('Y') . '/' . date('m') . '/' . date('d');
        FileSystem::createDir($basePathUpload . $profilePath);

        /**
         * Расчет параметров
         */

        $image_w = $image->image_src_x;
        $image_h = $image->image_src_y;
        $normal_w = $normal_width;
        $normal_h = $normal_height;
        $block_w = $preview_width;
        $block_h = $preview_height;
        $k = 1;
        $horiz = false;

        /* если фото вертикальное */
        if ($image_w > $image_h) {
            $horiz = true;
            $result['horizontal'] = 1;
        } else { /* если фото горизонтальное */
            $result['horizontal'] = 0;
            $horiz = false;
        }
        /* проверка больше ли изображения заданого стандарта */
        if (($image_w > $normal_w) || ($image_h > $normal_h)) {
            if ($horiz) {
                $k = $normal_w / $image_w;
            } else {
                $k = $normal_h / $image_h;
            }
            $image_w = round($image_w * $k);
            $image_h = round($image_h * $k);
        }

        $config = Environment::getContext()->getParameters('watermark');

        $watermark = $watermark ? $watermark : $config['normal']['path'];
        $watermark_type = $watermark_type ? $watermark_type : $config['normal']['type'];

        self::watermark($image, $watermark, $watermark_type);

        /*
         * сохранения нормально разрешени картинки
         */
        if ($old_normal != null) {
            $image->file_new_name_body = str_replace('.' . get(new \SplFileInfo(DIR_INDEX . $old_normal))->getExtension(), '', basename($old_normal));
            @unlink(DIR_INDEX . $old_normal);
        } else {
            $image->file_new_name_body = Strings::random(7, '0-9a-zA-Z');

        }
        $result['height'] = $image->image_y = $image_h;
        $result['width'] = $image->image_x = $image_w;
        $image->image_resize = true;
        $image->Process($basePathUpload . $profilePath);
        if ($image->processed) {
            $result['normal'] = Path::localPath(DIR_INDEX, $image->file_dst_pathname);
        } else {
            return false;
        }

        $image->file_new_name_body = Strings::random(7, '0-9a-zA-Z');

        if ($crop) {
            $image->image_ratio_crop = true;
        } elseif ($ratio_fill) {
            $image->image_ratio_fill = $ratio_fill;
        } else {
            $image->image_ratio = true;
        }

        if ($background_color) {
            $image->image_background_color = $background_color;
        }

        if (!$background_color && $ratio_fill) {
            $image->image_convert = 'png';
        }

        $watermark_preview = $watermark_preview ? $watermark_preview : $config['preview']['path'];
        $watermark_preview_type = $watermark_preview_type ? $watermark_preview_type : $config['preview']['type'];

        self::watermark($image, $watermark_preview, $watermark_preview_type);

        $image->image_resize = true;
        $result['pre_height'] = $image->image_y = $block_h;
        $result['pre_width'] = $image->image_x = $block_w;
        $image->Process($basePathUpload . $profilePath);
        if ($image->processed) {
            $result['preview'] = Path::localPath(DIR_INDEX, $image->file_dst_pathname);
            $image->Clean();
        } else {
            return false;
        }
        return $result;
    }

    /**
     * Возращает массив данных по разруженой картинке или false
     * @return array('horizontal','width','height','normal','preview')|false
     */
    public static function downloadByPath(
        $path,
        $dir = 'files',
        $normal_width = IMAGE_WIDTH,
        $normal_height = IMAGE_HEIGHT,
        $preview_width = IMAGE_PREVIEW_WIDTH,
        $preview_height = IMAGE_PREVIEW_HEIGHT,
        $crop = false,
        $ratio_fill = false,
        $background_color = false,
        $watermark = false,
        $watermark_type = false,
        $watermark_preview = false,
        $watermark_preview_type = false
    )
    {
        $result = array(
            'horizontal' => '',
            'width' => '',
            'height' => '',
            'normal' => '',
            'preview' => ''
        );

        $image = new \Upload($path);
        if (!$image->uploaded) {
            return false;
        }

        /*
         * путь до место хранения картинки
         */
        $basePath = Environment::getContext()->getParameters('path');
        $basePathUpload = $basePath['upload'];
        $profilePath = '/' . $dir . '/' . date('Y') . '/' . date('m') . '/' . date('d');
        FileSystem::createDir($basePathUpload . $profilePath);

        /**
         * Расчет параметров
         */

        $image_w = $image->image_src_x;
        $image_h = $image->image_src_y;
        $normal_w = $normal_width;
        $normal_h = $normal_height;
        $block_w = $preview_width;
        $block_h = $preview_height;
        $k = 1;
        $horiz = false;

        /* если фото вертикальное */
        if ($image_w > $image_h) {
            $horiz = true;
            $result['horizontal'] = 1;
        } else { /* если фото горизонтальное */
            $result['horizontal'] = 0;
            $horiz = false;
        }
        /* проверка больше ли изображения заданого стандарта */
        if (($image_w > $normal_w) || ($image_h > $normal_h)) {
            if ($horiz) {
                $k = $normal_w / $image_w;
            } else {
                $k = $normal_h / $image_h;
            }
            $image_w = round($image_w * $k);
            $image_h = round($image_h * $k);
        }

        $config = Environment::getContext()->getParameters('watermark');

        $watermark = $watermark ? $watermark : $config['normal']['path'];
        $watermark_type = $watermark_type ? $watermark_type : $config['normal']['type'];

        self::watermark($image, $watermark, $watermark_type);

        /*
         * сохранения нормально разрешени картинки
         */
        $image->file_new_name_body = Strings::random(7, '0-9a-zA-Z');
        $result['height'] = $image->image_y = $image_h;
        $result['width'] = $image->image_x = $image_w;
        $image->image_resize = true;
        $image->Process($basePathUpload . $profilePath);
        if ($image->processed) {
            $result['normal'] = Path::localPath(DIR_INDEX, $image->file_dst_pathname);
        } else {
            return false;
        }

        $image->file_new_name_body = Strings::random(7, '0-9a-zA-Z');

        if ($crop) {
            $image->image_ratio_crop = true;
        } elseif ($ratio_fill) {
            $image->image_ratio_fill = $ratio_fill;
        } else {
            $image->image_ratio = true;
        }

        if ($background_color) {
            $image->image_background_color = $background_color;
        }

        if (!$background_color && $ratio_fill) {
            $image->image_convert = 'png';
        }

        $watermark_preview = $watermark_preview ? $watermark_preview : $config['preview']['path'];
        $watermark_preview_type = $watermark_preview_type ? $watermark_preview_type : $config['preview']['type'];

        self::watermark($image, $watermark_preview, $watermark_preview_type);

        $image->image_resize = true;
        $result['pre_height'] = $image->image_y = $block_h;
        $result['pre_width'] = $image->image_x = $block_w;
        $image->Process($basePathUpload . $profilePath);
        if ($image->processed) {
            $result['preview'] = Path::localPath(DIR_INDEX, $image->file_dst_pathname);
            $image->Clean();
        } else {
            return false;
        }
        return $result;
    }

    public static function  watermark(\Upload $image, $watermark = false, $watermark_type = false)
    {
        if ($watermark) {
            $image->image_watermark = $watermark;
            switch ($watermark_type) {
                case 'rand':
                    $arr = array('BL', 'BR', 'TL', 'TR');
                    $watermark_type = $arr[rand(0, 3)];
                    break;
                case 'BL':
                case 'BR':
                case 'TL':
                case 'TR':
                    break;
                default:
                    $watermark_type = false;
            }
            if ($watermark_type) {
                $image->image_watermark_no_zoom_out = true;
                $image->image_watermark_position = $watermark_type;
            } else {
                $image->image_watermark_no_zoom_out = false;
            }
        }
    }

    /**
     * @param string $url Http url image
     * @return string
     * @throws \Delorius\Exception\Error
     */
    public static function httpDownload($url, $name = null, $sleep = 0)
    {
        $image = @file_get_contents($url);
        if ($image === false) {
            return null;
        }
        $upload = Environment::getContext()->getParameters('path.upload');
        $path_info = pathinfo($url);
        $file = $upload . '/http/' . ($name ? $name : md5($url)) . '.' . $path_info['extension'];
        FileSystem::write($file, $image);
        if ($sleep) {
            sleep($sleep);
        }
        return $file;
    }

} 