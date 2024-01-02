<?php
namespace CMS\Core\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;

class EditImageBehavior extends ORMBehavior {

    public function afterDelete(ORM $orm){
        @unlink(DIR_INDEX.$orm->normal);
        @unlink(DIR_INDEX.$orm->preview);
    }

} 