<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Helper\DocHelper;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;
use Delorius\Http\FileUpload;

class EditFileBehavior extends ORMBehavior {

    public $absolute = false;
    public $dir = 'document';
    public $timer = true;

    public function afterDelete(ORM $orm){
        if($orm->path)
            @unlink(DIR_INDEX.$orm->path);
    }

    /**
     * @param FileUpload $file
     * @return bool|array
     */
    public function setFile(FileUpload $file){

        $doc = $this->getOwner();
        if ($doc->loaded() && $doc->path) {
            @unlink(DIR_INDEX.$doc->path);
        }

        $result = DocHelper::download($file,$this->dir,$this->absolute,$this->timer);
        if (!$result) {
            return false;
        }

        try{
            $doc->values($result);
            $doc->save(true);
            return $doc->as_array();
        }catch (OrmValidationError $e){
            return false;
        }

    }

} 