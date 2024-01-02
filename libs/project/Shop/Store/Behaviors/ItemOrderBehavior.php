<?php
namespace Shop\Store\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Delorius\Core\ORM;
use Delorius\Exception\OrmValidationError;
use Shop\Store\Entity\Item;

class ItemOrderBehavior extends ORMBehavior
{
    /**
     * @return ORM|\Delorius\DataBase\Result
     */
    public function getItems()
    {
        $items = Item::model()->sort()->where('order_id','=',$this->getOwner()->pk());
        return $items->find_all();
    }

    /**
     * @param array $value
     * @return array|bool
     */
    public function addItem(array $value)
    {
        if (!$this->getOwner()->loaded()) {
            return false;
        }

        try {
            $item = new Item();
            $item->values($value);
            $item->order_id = $this->getOwner()->pk();
            $item->save(true);
            return $item->as_array();
        } catch (OrmValidationError $e) {
            return false;
        }

    }

    public function afterDelete(ORM $orm)
    {
        $items= $this->getItems();
        foreach ($items as $item) {
            $item->delete();
        }
        Item::model()->cache_delete();
    }

}