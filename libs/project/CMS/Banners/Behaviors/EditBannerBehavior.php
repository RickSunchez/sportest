<?php
namespace CMS\Banners\Behaviors;

use CMS\Banners\Entity\Banner;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Exception\Error;
use Delorius\Http\FileUpload;
use Delorius\Utils\Dir;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Path;
use Delorius\Utils\Strings;

class EditBannerBehavior extends ORMBehavior {

    /**
     * @param FileUpload $file
     * @return bool
     */
    public function setFile(FileUpload $file)
    {
        if (!$file->isOk()) {
            return false;
        }

        $basename = basename($file->getName());
        $res = explode('.', $basename);
        $ext = Strings::lower(array_pop($res));

        if (!in_array($ext, array('gif', 'png', 'jpeg', 'jpg', 'swf'))) {
            return false;
        }

        if ($file->isImage()) {
            $this->getOwner()->type_id = Banner::TYPE_IMAGE;
            $arr = $file->getImageSize();
            $this->getOwner()->width = $arr['0'];
            $this->getOwner()->height = $arr['1'];
        } elseif ($ext = 'swf') {
            $this->getOwner()->type_id = Banner::TYPE_FLASH;
        } else {
            throw new Error('Unknown type file');
        }

        $basePath = Environment::getContext()->getParameters('path');
        $basePathUpload = $basePath['upload'];
        $bannerPath = '/banners/' . date('Y') . '/' . date('m') . '/' . date('d');
        FileSystem::createDir($basePathUpload.$bannerPath);
        $path = $basePathUpload . $bannerPath . '/';

        $file->move($path, time() . '_' . $file->getName());
        $tmp_path = $this->getOwner()->path;
        $this->getOwner()->path = Path::localPath(DIR_INDEX, $file->getTemporaryFile());
        if ($tmp_path) {
            FileSystem::delete(DIR_INDEX . $tmp_path);
        }
        return true;
    }

    public function afterDelete(ORM $orm){
        if($orm->path)
            FileSystem::delete(DIR_INDEX . $orm->path);
    }
} 