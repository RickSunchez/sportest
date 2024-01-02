<?php
namespace CMS\Core\Helper;

use Delorius\Core\Environment;
use Delorius\Exception\Error;
use Delorius\Http\FileUpload;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Path;

class DocHelper
{
    /**
     * Возращает массив данных по разруженому файлу или false
     * @param FileUpload $file
     * @param string $_dir
     * @param bool $_absolute
     * @param bool $_timer
     * @return array|bool
     * @throws Error
     */
    public static function download(
        FileUpload $file,
        $_dir = 'document',
        $_absolute = false,
        $_timer = true
    )
    {
        $result = array(
            'path' => '',
            'ext' => '',
            'name' => '',
            'size' => 0
        );

        /*
         * путь до место хранения картинки
         */
        if (!$_absolute) {
            $basePathUpload = Environment::getContext()->getParameters('path.upload');
            $profilePath = '/' . $_dir . '/' . date('Y') . '/' . date('m') . '/' . date('d');
            $path = $basePathUpload . $profilePath . '/';
        } elseif (is_string($_absolute)) {
            $path = $_absolute;
        } else {
            throw new Error('Абсолютный путь не указан: ' . gettype($_absolute));
        }

        Environment::getContext()->getService('logger')->info($path, 'path');
        if (!is_dir($path)) {
            FileSystem::createDir($path);
        }

        $name = ($_timer ? time() . '_' : '') . $file->getName();

        try {
            $file->move($path, $name);
        } catch (Error $e) {
            return false;
        }

        $result['path'] = Path::localPath(DIR_INDEX, $file->getTemporaryFile());
        $result['name'] = $name;
        $result['ext'] = get(new \SplFileInfo($file->getTemporaryFile()))->getExtension();
        $result['size'] = $file->getSize();

        return $result;
    }

} 