<? if (count($goods)): ?>
    <h3 class="b-title-sub">Популярные товары</h3>
    <ul class="b-goods-list__layout hListing">

        <? foreach ($goods as $item): ?>
            <li class="b-goods-item b-goods-item_<?= $item->pk() ?>"
                id="goods_<?= $item->pk() ?> item hproduct">

                <?= $this->partial('shop/goods/_item_horiz', array(
                    'goods' => $item,
                    'basket' => $basket
                )) ?>

            </li>
        <? endforeach ?>

    </ul>
<? endif ?>