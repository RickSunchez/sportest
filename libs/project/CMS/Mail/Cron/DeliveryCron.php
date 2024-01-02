<?php
namespace CMS\Mail\Cron;


use CMS\Mail\Entity\Delivery;
use CMS\Mail\Entity\Subscriber;
use Delorius\Core\Cron;
use Delorius\Core\Environment;

class DeliveryCron extends Cron{

    protected function client()
    {
        $delivery = new Delivery();
        $delivery
            ->where('status','=',1)
            ->where('finished','=',0)
            ->order_by('delivery_id')
            ->find();

        if(!$delivery->loaded()){
            return;
        }

        $subscribers = new Subscriber();
        $subscribers->where('status', '=', 1)->order_by('ip', 'DESC');

        if($delivery->group_id){
            $subscribers->whereSubscriptionId($delivery->group_id);
        }

        if($delivery->started == 0){
            $arSub = $subscribers->find_all();
            $delivery->started = 1;
            $delivery->date_start = time();
            $delivery->count = $delivery->limit = $arSub->count();
            $delivery->offset = 0;
            $delivery->save(true);
        } else {
            $arSub = $subscribers->offset($delivery->offset)->limit($delivery->limit)->find_all();
        }



        foreach($arSub as $item){
            $delivery->offset++;
            $delivery->save(true);
            $item->sendMessage($delivery->subject,$delivery->message,$delivery->group_id);
            sleep(3);
        }


        if ($delivery->finished == 0 && $delivery->offset >= $delivery->count) {
            $message = "Тема: {$delivery->subject} | Кол-во подписчиков: {$delivery->count}  | start: " . date('d-m-Y H:i', $delivery->date_start) . " | end: " . date('d-m-Y H:i', $delivery->date_end);
            Environment::getContext()->getService('logger')->info($message, 'DeliveryCron');
            $delivery->finished = 1;
            $delivery->date_end = time();
            $delivery->save(true);
        }

    }
}