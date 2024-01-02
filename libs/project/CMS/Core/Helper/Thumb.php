<?php

namespace CMS\Core\Helper;


class Thumb
{

    /**
     * @param int $id
     * @param string $size = 100, 100x200, 100x, x100, <500 - Ширина и/или высота должна быть от 8 до 1500 px
     * @param int $crop = 0/1
     * @param int $trim = 0/1
     * @param int $zoom = 0/1
     * @param string $align = c, t, r, b, l, tl, tr, br, bl - Выравнивание изображения при обрезании
     * @param int $sharpen = 0 - 100  - Процентная сила резкости изображения, основанная на процентной средней точке 12 (сильная) и 28 (слабой)
     * @param int $gray = 0/1 - Преобразует изображение в оттенки серого
     * @param int $ignore = 0/1 - (gif) - Отображает исходный файл изображения с присутствующей анимацией
     * @return string
     */
    public static function linkById($id, $size = null, $crop = null, $trim = null, $zoom = null, $align = null, $sharpen = null, $gray = null, $ignore = null)
    {
        $params = array('image_id' => $id);
        if (is_scalar($align)) {
            $params['align'] = $align;
        }
        if (is_scalar($crop)) {
            $params['crop'] = $crop;
        }
        if (is_scalar($gray)) {
            $params['gray'] = $gray;
        }
        if (is_scalar($ignore)) {
            $params['ignore'] = $ignore;
        }
        if (is_scalar($sharpen)) {
            $params['sharpen'] = $sharpen;
        }
        if (is_scalar($size)) {
            $params['size'] = $size;
        }
        if (is_scalar($trim)) {
            $params['trim'] = $trim;
        }
        if (is_scalar($zoom)) {
            $params['zoom'] = $zoom;
        }

        return link_to('thumb_id', $params);

    }

    /**
     * @param int $id
     * @param string $size = 100, 100x200, 100x, x100, <500 - Ширина и/или высота должна быть от 8 до 1500 px
     * @param int $crop = 0/1
     * @param int $trim = 0/1
     * @param int $zoom = 0/1
     * @param string $align = c, t, r, b, l, tl, tr, br, bl - Выравнивание изображения при обрезании
     * @param int $sharpen = 0 - 100  - Процентная сила резкости изображения, основанная на процентной средней точке 12 (сильная) и 28 (слабой)
     * @param int $gray = 0/1 - Преобразует изображение в оттенки серого
     * @param int $ignore = 0/1 - (gif) - Отображает исходный файл изображения с присутствующей анимацией
     * @return string
     */
    public static function linkBySrc($src, $size = null, $crop = null, $trim = null, $zoom = null, $align = null, $sharpen = null, $gray = null, $ignore = null)
    {
        $params = array('image_src' => $src);
        if (is_scalar($align)) {
            $params['align'] = $align;
        }
        if (is_scalar($crop)) {
            $params['crop'] = $crop;
        }
        if (is_scalar($gray)) {
            $params['gray'] = $gray;
        }
        if (is_scalar($ignore)) {
            $params['ignore'] = $ignore;
        }
        if (is_scalar($sharpen)) {
            $params['sharpen'] = $sharpen;
        }
        if (is_scalar($size)) {
            $params['size'] = $size;
        }
        if (is_scalar($trim)) {
            $params['trim'] = $trim;
        }
        if (is_scalar($zoom)) {
            $params['zoom'] = $zoom;
        }

        return link_to('thumb_src', $params);
    }

}