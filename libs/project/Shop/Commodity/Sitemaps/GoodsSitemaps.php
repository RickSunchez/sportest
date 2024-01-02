<?php

namespace Shop\Commodity\Sitemaps;

use CMS\Core\Component\Sitemaps\Controls\BaseSitemaps;
use Delorius\DataBase\DB;
use Shop\Commodity\Entity\Goods;

class GoodsSitemaps extends BaseSitemaps
{
    /** @var string */
    protected $name = 'goods';

    public function initUrls()
    {
        $this->name .= '_' . $this->options['type_id'];
        $router = $this->options['router'];

        $count = Goods::model()->where('update', '=', 1)->count_all();

        $goods = Goods::model()
            ->select('goods_id', 'url', 'date_cr', 'date_edit')
            ->where('ctype', '=', $this->options['type_id'])
            ->order_by('date_edit', 'desc')
            ->order_by('date_cr')
            ->active()
            ->moder(false);

        if ($count) {
            $goods->where('update', '=', 1);
        }

        $result = $goods->find_all();

        foreach ($result as $item) {
            $this->addUrl(
                link_to($router, array('id' => $item['goods_id'], 'url' => $item['url'])),
                $item['date_edit'] ? $item['date_edit'] : $item['date_cr'],
                self::CHANGE_DAILY,
                0.7
            );
        }

        if ($count) {
            $table = Goods::model();
            DB::update($table->table_name())
                ->where('update', '=', 1)
                ->value('update', 0)
                ->execute($table->db_config());
        }
    }
}