<?php
namespace Shop\Store\Component\Status;

use CMS\Core\Component\Register;
use Delorius\Core\Environment;
use Delorius\DataBase\DB;
use Shop\Commodity\Entity\TypeGoods;

class NewCallback extends ACallback
{
    function run()
    {
        $config = Environment::getContext()->getParameters('shop.after_by');

        if ($config['type_id']) {
            $items = $this->order->getItems();
            foreach ($items as $item) {
                $type = new TypeGoods();
                $type->type_id = $config['type_id'];
                $type->goods_id = $item->goods_id;
                $type->save();
                $type = null;
            }

            $typeGoods = TypeGoods::model();
            $result = DB::select(array(DB::expr('COUNT(`id`)'), 'count'))
                ->from($typeGoods->table_name())
                ->where('type_id', '=', $config['type_id'])
                ->execute($typeGoods->db_config());
            $count = $result->get('count');

            if ($config['limit'] < $count) {
                $count_delete = $count - $config['limit'];
                DB::delete($typeGoods->table_name())
                    ->order_by('id')
                    ->limit($count_delete)
                    ->where('type_id', '=', $config['type_id'])
                    ->execute($typeGoods->db_config());
            }
        }

        Environment::getContext()->getService('register')->add(
            Register::TYPE_INFO,
            Register::SPACE_SITE,
            'Создан новый заказ: code=[number]',
            null,
            array('number' => $this->order->getNumber())
        );


        Environment::getContext()->getService('logger')->info(
            _sf('Create order by ID:{0}, price:{1}', $this->order->pk(), $this->order->getPrice(null, false)),
            'order-create-callback'
        );
    }

}