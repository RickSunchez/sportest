<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\File;
use CMS\Core\Helper\DocHelper;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;

class FileBehavior extends ORMBehavior {

    /**
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getFiles()
    {
        return File::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', $this->getOwner()->table_name())
            ->find_all();
    }

    /** @return array|bool */
    public function addFile($file)
    {
        if (!$this->getOwner()->loaded()) {
            return false;
        }
        $arrFile = DocHelper::download($file,'files');
        if( !$arrFile ){
            return false;
        }
        try {
            $file = new File();
            $file->values($arrFile);
            $file->target_id = $this->getOwner()->pk();
            $file->target_type = $this->getOwner()->table_name();
            $file->save(true);
            return $file->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /**
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getFile()
    {
        return File::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=', $this->getOwner()->table_name())
            ->find();
    }

    public function setFile($file){

        if (!$this->getOwner()->loaded()) {
            return false;
        }
        $arrFile = DocHelper::download($file,'files');
        if( !$arrFile ){
            return false;
        }

        $oldImage = $this->getFile();
        if($oldImage->loaded()){
            $oldImage->delete();
        }

        try {
            $file = new File();
            $file->values($arrFile);
            $file->target_id = $this->getOwner()->pk();
            $file->target_type = $this->getOwner()->table_name();
            $file->save(true);
            return $file->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }

    }


    public function afterDelete(ORM $orm){
        $files = $this->getFiles();
        foreach($files as $file){
            $file->delete();
        }
        if($files->count())
            $file->cache_delete();
    }



} 