<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\Tags;
use CMS\Core\Entity\TagsObject;
use CMS\Core\Helper\Helpers;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;

class TagsBehavior extends ORMBehavior
{

    public function afterDelete(ORM $orm)
    {
        $tagsObject = TagsObject::model()
            ->whereByTargetId($orm->pk())
            ->whereByTargetType($orm)
            ->find_all();
        foreach ($tagsObject as $item) {
            $item->delete();
        }
    }

    /**
     * @param $value
     * @param null $option
     * @throws \Delorius\Exception\Error
     */
    public function setTag($value)
    {
        $owner = $this->getOwner();
        if ($value['tag_id']) {

            $tag = Tags::model()
                ->where('tag_id', '=', $value['tag_id'])
                ->whereByTargetType($owner)
                ->find();

            if ($tag->loaded()) {
                $tagsObject = TagsObject::model()
                    ->whereByTargetId($owner->pk())
                    ->whereByTargetType($owner)
                    ->whereTagId($tag->pk())
                    ->find();

                if ($value['delete'] == 1) {
                    $tagsObject->delete();
                }
            }

        } else {

            $tag = Tags::model()
                ->whereByTargetType($owner)
                ->whereName($value['name'])
                ->find();

            if (!$tag->loaded()) {
                $tag->name = $value['name'];
                $tag->target_type = Helpers::getTableId($owner);
                $tag->save();
            }

            $tagsObject = new TagsObject();
            $tagsObject->tag_id = $tag->pk();
            $tagsObject->target_id = $owner->pk();
            $tagsObject->target_type = Helpers::getTableId($owner);
            $tagsObject->save();
        }
    }

    /**
     * @param null $option
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getTags($select = null)
    {
        $owner = $this->getOwner();
        $tags = new Tags();
        $tagsObject = new TagsObject();
        $tags->join($tagsObject->table_name(), 'inner')
            ->on($tags->table_name() . '.tag_id', '=', $tagsObject->table_name() . '.tag_id')
            ->where($tagsObject->table_name() . '.target_id', '=', $owner->pk())
            ->where($tagsObject->table_name() . '.target_type', '=', Helpers::getTableId($owner))
            ->where($tags->table_name() . '.target_type', '=', Helpers::getTableId($owner))
            ->sort();

        if (is_array($select)) {
            $_select = array();
            foreach ($select as $name) {
                $_select[] = $tags->table_name() . '.' . $name;
            }
            $tags->select_array($_select);
        }

        return $tags->find_all();
    }

    /**
     * @param string|Tags $name
     * @return Object
     * @throws \Delorius\Exception\Error
     */
    public function whereTagName($name)
    {
        $orm = $this->getOwner();
        $tag = Tags::model()
            ->whereByTargetType($orm)
            ->whereName($name)
            ->find();

        if (!$tag->loaded()) {
            return $orm;
        }

        return $this->whereByTag($tag);
    }

    /**
     * @param $url
     * @return Object
     * @throws \Delorius\Exception\Error
     */
    public function whereTagUrl($url)
    {
        $orm = $this->getOwner();

        $tag = Tags::model()
            ->whereByTargetType($orm)
            ->whereUrl($url)
            ->find();

        if (!$tag->loaded()) {
            return $orm;
        }

        return $this->whereByTag($tag);
    }


    /**
     * @param $url
     * @return Object
     * @throws \Delorius\Exception\Error
     */
    public function whereByTag(Tags $tag)
    {
        $orm = $this->getOwner();

        $tagsObject = new TagsObject();
        if ($tag->loaded()) {
            $orm->join($tagsObject->table_name(), 'inner')
                ->on($tagsObject->table_name() . '.target_id', '=', $orm->table_name() . '.' . $orm->primary_key())
                ->where($tagsObject->table_name() . '.target_type', '=', Helpers::getTableId($orm))
                ->where($tagsObject->table_name() . '.tag_id', '=', $tag->pk());
        }
        return $orm;
    }


}