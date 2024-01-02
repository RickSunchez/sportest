<?php

namespace Shop\Catalog\Behaviors;

use Delorius\Behaviors\ORMBehavior;
use Shop\Catalog\Entity\Collection;

class HelpCollectionBehavior extends ORMBehavior
{

    /**
     * @param array|null $filters
     * @return mixed
     */
    public function mergeRequest($filters = null)
    {
        if (!$filters) {
            $filters = array();
        }
        /**
         * @var Collection
         */
        $orm = $this->getOwner();

        if ($orm->cats) {
            $cats = explode(',', $orm->cats);
            foreach ($cats as $id) {
                $filters['cats'][] = $id;
            }
            $filters['cats'] = array_unique($filters['cats']);
        }

        if ($orm->vendors) {
            $vendors = explode(',', $orm->vendors);
            foreach ($vendors as $id) {
                $filters['vendors'][$id] = $id;
            }
        }

        if ($orm->goods) {
            $filters['goods'] = explode(',', $orm->goods);
        }

        if ($orm->price_min > 0) {
            if ($filters['price_min'] < $orm->price_min)
                $filters['price_min'] = $orm->price_min;
        }

        if ($orm->price_max > 0) {
            if ($filters['price_max'] > $orm->price_max)
                $filters['price_max'] = $orm->price_max;
            elseif ($filters['price_max'] == 0)
                $filters['price_max'] = $orm->price_max;
        }

        $characteristics = $orm->getValueCharacteristics();

        if (count($characteristics)) {
            foreach ($characteristics as $item) {
                $filters['feature'][$item->character_id][$item->value_id] = $item->value_id;
            }
        }

        return $filters;
    }
}