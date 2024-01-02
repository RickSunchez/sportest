<?php
namespace Shop\Commodity\Behaviors;

use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\DataBase\DB;
use Delorius\Exception\OrmValidationError;
use Shop\Commodity\Entity\Section;


class SectionBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        DB::delete(Section::model()->table_name())
            ->where('target_id', '=', $orm->pk())
            ->where('target_type', '=', Helpers::getTableId($orm->table_name()))
            ->execute(Section::model()->db_config());
    }

    /** @return \Delorius\DataBase\Result */
    public function getSections($active = true,$first = false)
    {
        $section = Section::model()
            ->where('target_id', '=', $this->getOwner()->pk())
            ->where('target_type', '=',  Helpers::getTableId($this->getOwner()->table_name()))
            ->sort();
        if ($active) {
            $section->active();
        }
        if($first){
            return $section->find();
        }else{
            return $section->find_all();
        }
    }

    public function addSection($section)
    {
        try {
            $ormSection = new Section($section[Section::model()->primary_key()]);
            if($section['delete'] == 1){
                if ($ormSection->loaded()) {
                    $ormSection->delete();
                }
                return true;
            }
            $ormSection->values($section);
            $ormSection->target_id = $this->getOwner()->pk();
            $ormSection->target_type =  Helpers::getTableId($this->getOwner()->table_name());
            $ormSection->save(true);
            return true;
        } catch (OrmValidationError $e) {
            return false;
        }
    }

} 