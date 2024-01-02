<?php
namespace Shop\Store\Cron;

use Delorius\Core\Cron;
use Shop\Store\Component\CBRAgent;
use Shop\Store\Entity\Currency;

class CBRCron extends Cron
{

    protected function client()
    {
        $cbr = new CBRAgent();
        if ($cbr->load()) {
            $currency = Currency::model()->where('code', '<>', SYSTEM_CURRENCY)->find_all();
            foreach ($currency as $item) {
                if ($result = $cbr->get($item->code)) {
                    $item->values($result);
                    $item->save(true);
                }
            }
        }

        $this->isEnd('Валюта обновлена');
    }
}