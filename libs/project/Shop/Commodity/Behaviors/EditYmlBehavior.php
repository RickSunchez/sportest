<?php

namespace Shop\Commodity\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\Environment;
use Delorius\Core\ORM;
use Delorius\Utils\FileSystem;
use Delorius\Utils\Path;

class EditYmlBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $this->deleteFileXML();
    }


    public function getPath($absolute = true)
    {
        $path = _sf('{0}/{1}.xml', Environment::getContext()->getParameters('path.market'), $this->getOwner()->file);
        if (!$absolute) {
            $path = Path::localPath(DIR_INDEX, $path);
        }
        return $path;
    }

    public function isExists()
    {
        $path = $this->getPath();
        return file_exists($path) ? true : false;
    }

    public function deleteFileXML()
    {
        FileSystem::delete($this->getPath());
    }

} 