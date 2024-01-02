<?php
namespace CMS\Core\Behaviors;

use CMS\Core\Entity\ItemPoll;
use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;

class EditPollBehavior extends ORMBehavior
{

    public function beforeDelete(ORM $orm)
    {
        $items = $this->getItems();
        foreach ($items as $item) {
            $item->delete();
        }
        ItemPoll::model()->cache_delete();
    }

    /**
     * @param $value
     * @return array|bool
     */
    public function addItem($value)
    {
        if (!$this->getOwner()->loaded()) {
            return false;
        }

        try {
            $item = new ItemPoll($value[ItemPoll::model()->primary_key()]);
            if ($item->loaded() && $value['delete'] == 1) {
                $item->delete(true);
                return false;
            }
            $item->values($value);
            $item->poll_id = $this->getOwner()->pk();
            $item->save(true);
            return $item->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }
    }

    /**
     * @param $item_id
     * @return bool
     */
    public function vote($item_id)
    {
        $item = new ItemPoll($item_id);
        if ($item->loaded() && $item->poll_id == $this->getOwner()->poll_id) {
            $item->count += 1;
            $item->save(true);
            return true;
        }
        return false;
    }

    /**
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getItems()
    {
        $items = ItemPoll::model()
            ->where('poll_id', '=', $this->getOwner()->pk())
            ->sort()
            ->cached()
            ->find_all();
        return $items;
    }

} 