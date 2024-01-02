<?php

namespace CMS\Core\Controller;

use CMS\Core\Entity\Image;
use Delorius\Application\UI\Controller;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Strings;

class ThumbController extends Controller
{

    protected $config = array();

    public function byIdAction($image_id, $set)
    {
        $this->config = $this->container->getParameters('thumb');

        if (!($params = $this->config['set'][$set])) {
            $this->no_photo();
        }

        $size = isset($params['size']) ? str_replace(array('<', 'x'), '', $params['size']) != '' ? $params['size'] : 100 : 100;
        $crop = isset($params['crop']) ? max(0, min(1, $params['crop'])) : 1;
        $trim = isset($params['trim']) ? max(0, min(1, $params['trim'])) : 0;
        $zoom = isset($params['zoom']) ? max(0, min(1, $params['zoom'])) : 0;
        $align = isset($params['align']) ? $params['align'] : false;
        $sharpen = isset($params['sharpen']) ? max(0, min(100, $params['sharpen'])) : 0;
        $gray = isset($params['gray']) ? max(0, min(1, $params['gray'])) : 0;
        $ignore = isset($params['ignore']) ? max(0, min(1, $params['ignore'])) : 0;
        $type_image = isset($params['type']) ? $params['type'] : 'preview';
        $target_type = isset($params['target']) ? $params['target'] : false;

        $file_salt = 'v1';
        $file_hash = md5($file_salt . ($size . $crop . $trim . $zoom . $align . $sharpen . $gray . $ignore) . $target_type . $type_image);
        $image_id_temp = str_split($image_id);
        $dir_temp = _sf('{0}/orm/{1}/{2}/', $this->config['path'], $image_id_temp[0], $image_id);
        $file_temp = _sf('{0}{1}.img', $dir_temp, $file_hash);
        $file_temp_conf = _sf('{0}{1}.conf', $dir_temp, $file_hash);

        if (!file_exists($file_temp_conf)) {
            FileSystem::createDir($dir_temp);
            $image = new Image($image_id);

            if (!$image->loaded()) {
                $this->no_photo();
            }

            if ($target_type && $image->target_type != $target_type) {
                $this->no_photo();
            }

            if ($type_image == 'preview') {
                $src = $image->preview;
                $w0 = $image->pre_width;
                $h0 = $image->pre_height;
            } else {
                $src = $image->normal;
                $w0 = $image->width;
                $h0 = $image->height;
            }


            $file_time = time();
            $file_date = gmdate('D, d M Y H:i:s T', $file_time);
            $path = DIR_INDEX . $src;
            if (!file_exists($path)) {
                $this->no_photo();
            }
            $file_size = filesize($path);
            $file_name = basename(substr($src, 0, strrpos($src, '.')) . strtolower(strrchr($src, '.')));
            $ext = strtolower(substr(strrchr($src, '.'), 1));
            $data = file_get_contents($path);

            if ($image->name) {
                $name = Strings::webalize(Strings::translit(Strings::trim($image->name)));
                $name .= '.' . $ext;
            }

            if ($ignore && $ext == 'gif') {
                if (preg_match('/\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)/s', $data)) {
                    header('Content-Type: image/gif');
                    header('Content-Length: ' . $file_size);
                    header('Content-Disposition: inline; filename="' . $file_name . '"');
                    header('Last-Modified: ' . $file_date);
                    header('ETag: ' . $file_hash);
                    header('Accept-Ranges: none');
                    if (THUMB_BROWSER_CACHE) {
                        header('Cache-Control: max-age=604800, must-revalidate');
                        header('Expires: ' . gmdate('D, d M Y H:i:s T', strtotime('+7 days')));
                    } else {
                        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                        header('Expires: ' . gmdate('D, d M Y H:i:s T'));
                        header('Pragma: no-cache');
                    }
                    die($data);
                }
            }

            $oi = imagecreatefromstring($data);
            if (function_exists('exif_read_data') &&
                ADJUST_ORIENTATION &&
                ($ext == 'jpg' || $ext == 'jpeg')
            ) {
                $exif = @exif_read_data($src, EXIF);
                if (isset($exif['Orientation'])) {
                    $degree = 0;
                    $mirror = false;
                    switch ($exif['Orientation']) {
                        case 2:
                            $mirror = true;
                            break;
                        case 3:
                            $degree = 180;
                            break;
                        case 4:
                            $degree = 180;
                            $mirror = true;
                            break;
                        case 5:
                            $degree = 270;
                            $mirror = true;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                        case 6:
                            $degree = 270;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                        case 7:
                            $degree = 90;
                            $mirror = true;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                        case 8:
                            $degree = 90;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                    }
                    if ($degree > 0) {
                        $oi = imagerotate($oi, $degree, 0);
                    }
                    if ($mirror) {
                        $nm = $oi;
                        $oi = imagecreatetruecolor($w0, $h0);
                        imagecopyresampled($oi, $nm, 0, 0, $w0 - 1, 0, $w0, $h0, -$w0, $h0);
                        imagedestroy($nm);
                    }
                }
            }

            list($w, $h) = explode('x', str_replace('<', '', $size) . 'x');
            $w = ($w != '') ? floor(max(8, min(1500, $w))) : '';
            $h = ($h != '') ? floor(max(8, min(1500, $h))) : '';
            if (strstr($size, '<')) {
                $h = $w;
                $crop = 0;
                $trim = 1;
            } elseif (!strstr($size, 'x')) {
                $h = $w;
            } elseif ($w == '' || $h == '') {
                $w = ($w == '') ? ($w0 * $h) / $h0 : $w;
                $h = ($h == '') ? ($h0 * $w) / $w0 : $h;
                $crop = 0;
                $trim = 1;
            }
            $trim_w = ($trim) ? 1 : ($w == '') ? 1 : 0;
            $trim_h = ($trim) ? 1 : ($h == '') ? 1 : 0;

            if ($crop) {
                $w1 = (($w0 / $h0) > ($w / $h)) ? floor($w0 * $h / $h0) : $w;
                $h1 = (($w0 / $h0) < ($w / $h)) ? floor($h0 * $w / $w0) : $h;
                if (!$zoom) {
                    if ($h0 < $h || $w0 < $w) {
                        $w1 = $w0;
                        $h1 = $h0;
                    }
                }
            } else {
                $w1 = (($w0 / $h0) < ($w / $h)) ? floor($w0 * $h / $h0) : floor($w);
                $h1 = (($w0 / $h0) > ($w / $h)) ? floor($h0 * $w / $w0) : floor($h);
                $w = floor($w);
                $h = floor($h);
                if (!$zoom) {
                    if ($h0 < $h && $w0 < $w) {
                        $w1 = $w0;
                        $h1 = $h0;
                    }
                }
            }
            $w = ($trim_w) ? (($w0 / $h0) > ($w / $h)) ? min($w, $w1) : $w1 : $w;
            $h = ($trim_h) ? (($w0 / $h0) < ($w / $h)) ? min($h, $h1) : $h1 : $h;

            if ($sharpen) {
                $matrix = array(
                    array(-1, -1, -1),
                    array(-1, SHARPEN_MAX - ($sharpen * (SHARPEN_MAX - SHARPEN_MIN)) / 100, -1),
                    array(-1, -1, -1));
                $divisor = array_sum(array_map('array_sum', $matrix));
            }
            $x = strpos($align, 'l') !== false ? 0 : (strpos($align, 'r') !== false ? $w - $w1 : ($w - $w1) / 2);
            $y = strpos($align, 't') !== false ? 0 : (strpos($align, 'b') !== false ? $h - $h1 : ($h - $h1) / 2);
            $im = imagecreatetruecolor($w, $h);
            $bg = imagecolorallocate($im, 255, 255, 255);
            imagefill($im, 0, 0, $bg);
            switch ($ext) {
                case 'gif':
                    imagecopyresampled($im, $oi, $x, $y, 0, 0, $w1, $h1, $w0, $h0);
                    if ($sharpen && version_compare(PHP_VERSION, '5.1.0', '>=')) {
                        imageconvolution($im, $matrix, $divisor, 0);
                    }
                    if ($gray) {
                        imagefilter($im, IMG_FILTER_GRAYSCALE);
                    }
                    imagegif($im, $file_temp);
                    break;
                case 'jpg':
                case 'jpeg':
                    imagecopyresampled($im, $oi, $x, $y, 0, 0, $w1, $h1, $w0, $h0);
                    if ($sharpen && version_compare(PHP_VERSION, '5.1.0', '>=')) {
                        imageconvolution($im, $matrix, $divisor, 0);
                    }
                    if ($gray) {
                        imagefilter($im, IMG_FILTER_GRAYSCALE);
                    }
                    imagejpeg($im, $file_temp, JPEG_QUALITY);
                    break;
                case 'png':
                    imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
                    imagesavealpha($im, true);
                    imagealphablending($im, false);
                    imagecopyresampled($im, $oi, $x, $y, 0, 0, $w1, $h1, $w0, $h0);
                    if ($sharpen && version_compare(PHP_VERSION, '5.1.0', '>=')) {
                        $fix = imagecolorat($im, 0, 0);
                        imageconvolution($im, $matrix, $divisor, 0);
                        imagesetpixel($im, 0, 0, $fix);
                    }
                    if ($gray) {
                        imagefilter($im, IMG_FILTER_GRAYSCALE);
                    }
                    imagepng($im, $file_temp);
                    break;
            }
            imagedestroy($im);
            imagedestroy($oi);

            $file_size = filesize($file_temp);
            $arr = array($ext, $file_name, $file_time, $file_size, $name);
            file_put_contents($file_temp_conf, implode('|', $arr), LOCK_EX);


        } else {

            $contents = file_get_contents($file_temp_conf);
            $arr = explode('|', $contents);
            $ext = $arr[0];
            $file_name = $arr[1];
            $file_time = $arr[2];
            $file_date = gmdate('D, d M Y H:i:s T', $file_time);
            $file_size = $arr[3];
            $name = $arr[4];
            if (THUMB_BROWSER_CACHE) {
                if (!$this->httpContext->isModified($file_date, $file_hash)) {
                    $this->endProgram(true);
                }
            }

        }

        $this->httpResponse->setHeader('Content-Type', 'image/' . $ext);
        $this->httpResponse->setHeader('Content-Length', $file_size);
        $this->httpResponse->setHeader('Content-Disposition', 'filename="' . $image_id . '-t' . $set . '_size-' . $size . '_' . ($name ? $name : $file_name) . '"');
        $this->httpResponse->setHeader('Accept-Ranges', 'bytes');
        $this->httpResponse->setHeader('Last-Modified', $file_date);
        $this->httpResponse->setHeader('ETag', $file_hash);

        if (THUMB_BROWSER_CACHE) {
            $this->httpResponse->setHeader('Cache-Control', 'max-age=604800, must-revalidate');
            $this->httpResponse->setHeader('Pragma', 'cache');
            $this->httpResponse->setHeader('Expires', gmdate('D, d M Y H:i:s T', strtotime('+7 days')));
        } else {
            $this->httpResponse->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $this->httpResponse->setHeader('Pragma', 'no-cache');
            $this->httpResponse->setHeader('Expires', gmdate('D, d M Y H:i:s T'));
        }

        readfile($file_temp);
        die;
    }

    public function byPathAction($set, $src)
    {

        $this->config = $this->container->getParameters('thumb');

        if (!($params = $this->config['set'][$set])) {
            $this->no_photo();
        }

        if ($params['target'] != 'path') {
            $this->no_photo();
        }

        $parser = parse_url($src);
        $result = false;
        if (count($this->config['rules'])) {
            foreach ($this->config['rules'] as $rule) {
                $result = strpos($parser['path'], $rule);
                if ($result == 0) {
                    $result = true;
                    break;
                }
            }
        }

        if ($result === false) {
            $this->no_photo();
        }

        $path = DIR_INDEX . $src;
        if (!file_exists($path)) {
            $this->no_photo();
        }


        $size = isset($params['size']) ? str_replace(array('<', 'x'), '', $params['size']) != '' ? $params['size'] : 100 : 100;
        $crop = isset($params['crop']) ? max(0, min(1, $params['crop'])) : 1;
        $trim = isset($params['trim']) ? max(0, min(1, $params['trim'])) : 0;
        $zoom = isset($params['zoom']) ? max(0, min(1, $params['zoom'])) : 0;
        $align = isset($params['align']) ? $params['align'] : false;
        $sharpen = isset($params['sharpen']) ? max(0, min(100, $params['sharpen'])) : 0;
        $gray = isset($params['gray']) ? max(0, min(1, $params['gray'])) : 0;
        $ignore = isset($params['ignore']) ? max(0, min(1, $params['ignore'])) : 0;


        $file_salt = 'v1';
        $file_size = filesize($path);
        $file_time = filemtime($path);
        $file_date = gmdate('D, d M Y H:i:s T', $file_time);
        $ext = strtolower(substr(strrchr($src, '.'), 1));
        $file_hash = md5($file_salt . ($src . $size . $crop . $trim . $zoom . $align . $sharpen . $gray . $ignore) . $file_time);
        $file_hash_temp = str_split($file_hash, 2);
        $dir_temp = _sf('{0}/path/{1}/{2}/', $this->config['path'], $file_hash_temp[0], $file_hash_temp[1]);
        $file_temp = _sf('{0}{1}.img', $dir_temp, $file_hash);
        $file_temp_conf = _sf('{0}{1}.conf', $dir_temp, $file_hash);


        if (!file_exists($file_temp_conf)) {
            FileSystem::createDir($dir_temp);

            list($w0, $h0, $type) = getimagesize($path);
            $data = file_get_contents($path);
            $file_name = basename(substr($src, 0, strrpos($src, '.')) . strtolower(strrchr($src, '.')));

            if ($ignore && $ext == 'gif') {
                if (preg_match('/\x00\x21\xF9\x04.{4}\x00(\x2C|\x21)/s', $data)) {
                    header('Content-Type: image/gif');
                    header('Content-Length: ' . $file_size);
                    header('Content-Disposition: inline; filename="' . $file_name . '"');
                    header('Last-Modified: ' . $file_date);
                    header('ETag: ' . $file_hash);
                    header('Accept-Ranges: none');
                    if (THUMB_BROWSER_CACHE) {
                        header('Cache-Control: max-age=604800, must-revalidate');
                        header('Expires: ' . gmdate('D, d M Y H:i:s T', strtotime('+7 days')));
                    } else {
                        header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');
                        header('Expires: ' . gmdate('D, d M Y H:i:s T'));
                        header('Pragma: no-cache');
                    }
                    die($data);
                }
            }

            $oi = imagecreatefromstring($data);
            if (function_exists('exif_read_data') &&
                ADJUST_ORIENTATION &&
                ($ext == 'jpg' || $ext == 'jpeg')
            ) {
                $exif = @exif_read_data($src, EXIF);
                if (isset($exif['Orientation'])) {
                    $degree = 0;
                    $mirror = false;
                    switch ($exif['Orientation']) {
                        case 2:
                            $mirror = true;
                            break;
                        case 3:
                            $degree = 180;
                            break;
                        case 4:
                            $degree = 180;
                            $mirror = true;
                            break;
                        case 5:
                            $degree = 270;
                            $mirror = true;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                        case 6:
                            $degree = 270;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                        case 7:
                            $degree = 90;
                            $mirror = true;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                        case 8:
                            $degree = 90;
                            $w0 ^= $h0 ^= $w0 ^= $h0;
                            break;
                    }
                    if ($degree > 0) {
                        $oi = imagerotate($oi, $degree, 0);
                    }
                    if ($mirror) {
                        $nm = $oi;
                        $oi = imagecreatetruecolor($w0, $h0);
                        imagecopyresampled($oi, $nm, 0, 0, $w0 - 1, 0, $w0, $h0, -$w0, $h0);
                        imagedestroy($nm);
                    }
                }
            }

            list($w, $h) = explode('x', str_replace('<', '', $size) . 'x');
            $w = ($w != '') ? floor(max(8, min(1500, $w))) : '';
            $h = ($h != '') ? floor(max(8, min(1500, $h))) : '';
            if (strstr($size, '<')) {
                $h = $w;
                $crop = 0;
                $trim = 1;
            } elseif (!strstr($size, 'x')) {
                $h = $w;
            } elseif ($w == '' || $h == '') {
                $w = ($w == '') ? ($w0 * $h) / $h0 : $w;
                $h = ($h == '') ? ($h0 * $w) / $w0 : $h;
                $crop = 0;
                $trim = 1;
            }
            $trim_w = ($trim) ? 1 : ($w == '') ? 1 : 0;
            $trim_h = ($trim) ? 1 : ($h == '') ? 1 : 0;

            if ($crop) {
                $w1 = (($w0 / $h0) > ($w / $h)) ? floor($w0 * $h / $h0) : $w;
                $h1 = (($w0 / $h0) < ($w / $h)) ? floor($h0 * $w / $w0) : $h;
                if (!$zoom) {
                    if ($h0 < $h || $w0 < $w) {
                        $w1 = $w0;
                        $h1 = $h0;
                    }
                }
            } else {
                $w1 = (($w0 / $h0) < ($w / $h)) ? floor($w0 * $h / $h0) : floor($w);
                $h1 = (($w0 / $h0) > ($w / $h)) ? floor($h0 * $w / $w0) : floor($h);
                $w = floor($w);
                $h = floor($h);
                if (!$zoom) {
                    if ($h0 < $h && $w0 < $w) {
                        $w1 = $w0;
                        $h1 = $h0;
                    }
                }
            }
            $w = ($trim_w) ? (($w0 / $h0) > ($w / $h)) ? min($w, $w1) : $w1 : $w;
            $h = ($trim_h) ? (($w0 / $h0) < ($w / $h)) ? min($h, $h1) : $h1 : $h;

            if ($sharpen) {
                $matrix = array(
                    array(-1, -1, -1),
                    array(-1, SHARPEN_MAX - ($sharpen * (SHARPEN_MAX - SHARPEN_MIN)) / 100, -1),
                    array(-1, -1, -1));
                $divisor = array_sum(array_map('array_sum', $matrix));
            }
            $x = strpos($align, 'l') !== false ? 0 : (strpos($align, 'r') !== false ? $w - $w1 : ($w - $w1) / 2);
            $y = strpos($align, 't') !== false ? 0 : (strpos($align, 'b') !== false ? $h - $h1 : ($h - $h1) / 2);
            $im = imagecreatetruecolor($w, $h);
            $bg = imagecolorallocate($im, 255, 255, 255);
            imagefill($im, 0, 0, $bg);
            switch ($ext) {
                case 'gif':
                    imagecopyresampled($im, $oi, $x, $y, 0, 0, $w1, $h1, $w0, $h0);
                    if ($sharpen && version_compare(PHP_VERSION, '5.1.0', '>=')) {
                        imageconvolution($im, $matrix, $divisor, 0);
                    }
                    if ($gray) {
                        imagefilter($im, IMG_FILTER_GRAYSCALE);
                    }
                    imagegif($im, $file_temp);
                    break;
                case 'jpg':
                case 'jpeg':
                    imagecopyresampled($im, $oi, $x, $y, 0, 0, $w1, $h1, $w0, $h0);
                    if ($sharpen && version_compare(PHP_VERSION, '5.1.0', '>=')) {
                        imageconvolution($im, $matrix, $divisor, 0);
                    }
                    if ($gray) {
                        imagefilter($im, IMG_FILTER_GRAYSCALE);
                    }
                    imagejpeg($im, $file_temp, JPEG_QUALITY);
                    break;
                case 'png':
                    imagefill($im, 0, 0, imagecolorallocatealpha($im, 0, 0, 0, 127));
                    imagesavealpha($im, true);
                    imagealphablending($im, false);
                    imagecopyresampled($im, $oi, $x, $y, 0, 0, $w1, $h1, $w0, $h0);
                    if ($sharpen && version_compare(PHP_VERSION, '5.1.0', '>=')) {
                        $fix = imagecolorat($im, 0, 0);
                        imageconvolution($im, $matrix, $divisor, 0);
                        imagesetpixel($im, 0, 0, $fix);
                    }
                    if ($gray) {
                        imagefilter($im, IMG_FILTER_GRAYSCALE);
                    }
                    imagepng($im, $file_temp);
                    break;
            }
            imagedestroy($im);
            imagedestroy($oi);

            $file_size = filesize($file_temp);
            $arr = array($ext, $file_name, $file_time, $file_size);
            file_put_contents($file_temp_conf, implode('|', $arr), LOCK_EX);


        } else {

            $contents = file_get_contents($file_temp_conf);
            $arr = explode('|', $contents);
            $ext = $arr[0];
            $file_name = $arr[1];
            $file_time = $arr[2];
            $file_date = gmdate('D, d M Y H:i:s T', $file_time);
            $file_size = $arr[3];
            if (THUMB_BROWSER_CACHE) {
                if (!$this->httpContext->isModified($file_date, $file_hash)) {
                    $this->endProgram(true);
                }
            }

        }


        $this->httpResponse->setHeader('Content-Type', 'image/' . $ext);
        $this->httpResponse->setHeader('Content-Length', $file_size);
        $this->httpResponse->setHeader('Content-Disposition', 'filename="' . 't' . $set . '_size-' . $size . '_' . $file_name . '"');
        $this->httpResponse->setHeader('Accept-Ranges', 'bytes');
        $this->httpResponse->setHeader('Last-Modified', $file_date);
        $this->httpResponse->setHeader('ETag', $file_hash);

        if (THUMB_BROWSER_CACHE) {
            $this->httpResponse->setHeader('Cache-Control', 'max-age=604800, must-revalidate');
            $this->httpResponse->setHeader('Pragma', 'cache');
            $this->httpResponse->setHeader('Expires', gmdate('D, d M Y H:i:s T', strtotime('+7 days')));
        } else {
            $this->httpResponse->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
            $this->httpResponse->setHeader('Pragma', 'no-cache');
            $this->httpResponse->setHeader('Expires', gmdate('D, d M Y H:i:s T'));
        }

        readfile($file_temp);
        die;
    }


    protected function no_photo()
    {

        $src = $this->config['no_photo'];
        $path = DIR_INDEX . $src;
        $ext = strtolower(substr(strrchr($src, '.'), 1));
        $file_size = filesize($path);
        $file_name = basename(substr($src, 0, strrpos($src, '.')) . strtolower(strrchr($src, '.')));

        $this->httpResponse->setHeader('Content-Type', 'image/' . $ext);
        $this->httpResponse->setHeader('Content-Length', $file_size);
        $this->httpResponse->setHeader('Content-Disposition', 'inline; filename="' . $file_name . '"');
        $this->httpResponse->setHeader('Accept-Ranges', 'bytes');
        $this->httpResponse->setHeader('Cache-Control', 'no-store, no-cache, must-revalidate, max-age=0');
        $this->httpResponse->setHeader('Pragma', 'no-cache');
        $this->httpResponse->setHeader('Expires', gmdate('D, d M Y H:i:s T'));

        readfile($path);
        die;
    }

}