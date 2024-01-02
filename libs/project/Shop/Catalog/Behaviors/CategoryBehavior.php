<?php
namespace Shop\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;

class CategoryBehavior extends ORMBehavior
{

    public function sort($direction = 'DESC')
    {
        $orm = $this->getOwner();
        $orm->order_by($orm->table_name() . '.pos', $direction)
            ->order_by($orm->table_name() . '.popular', 'desc')
            ->order_pk();
        return $orm;
    }

}