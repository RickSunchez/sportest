<?php

namespace Shop\Commodity\Cron;

use Delorius\Core\Cron;
use Shop\Commodity\Component\YandexMarker\YmlGenerator;
use Shop\Commodity\Entity\Goods;

class MarketAllCron extends Cron
{

    protected function client()
    {
        $ymls = \Shop\Commodity\Entity\YmlGenerator::model()->order_pk()->find_all();

        foreach ($ymls as $yml) {
            $type_id = $yml->ctype;
            $cids = $yml->getConfig();
            if (count($cids)) {
                $gen = new YmlGenerator($type_id);
                $goods = Goods::model()
                    ->select_array($gen->select)
                    ->active()
                    ->where('ctype', '=', $type_id)
                    ->where('cid', 'in', $cids);
                if ($yml->amount) {
                    $goods->where('is_amount', '=', 1);
                }
                $result = $goods->find_all();
                $gen->create($result, $yml->as_array());
            }
        }
    }


}