<?php
namespace CMS\Go\Behaviors;

use CMS\Core\Entity\Image;
use CMS\Core\Helper\ImageHelper;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Http\FileUpload;

class EditGoBehavior extends ORMBehavior {

    public function onAfterDelete(ORM $orm){
        $orm->clearStatistics();
    }

} 