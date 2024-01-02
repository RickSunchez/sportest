<article class="b-page__show">
    <header class="b-page__header">
        <h1 class="b-page__title">Данные по заказу № <?= $order->getNumber() ?></h1>
        <time datetime="<?= date('Y-m-d', $order->date_cr) ?>">
            Дата оформления: <?= \Delorius\Core\DateTime::dateFormat($order->date_cr, true); ?>
        </time>
    </header>

    <div class="b-text b-page-show__text">

        <p><b>Статус заказа:</b> <?= $order->getStatusName(); ?></p>
        <? if (!$order->paid): ?>
            <?= $order_cart->getPayment()->render() ?>
        <? endif; ?>

        <? if ($order_cart->getDelivery()->isActive()): ?>
            <p><b>Доставка</b>: <?= $order_cart->getDelivery()->getName() ?></p>
            <? if ($order_cart->getDelivery()->getDesc()): ?>
                <p>Примечание: <?= $order_cart->getDelivery()->getDesc() ?></p>
            <? endif; ?>
        <? endif; ?>


        <div class="b-order__contacts b-contacts">
            <h2>Контактные данные</h2>
            <? foreach ($order->getOptions() as $opt): ?>
                <div class="value">
                    <?= $opt['name'] ?>: <?= $opt['value'] ?>
                </div>
            <? endforeach ?>
        </div>




        <table class="table table-bordered table-cart-goods">
            <tr>
                <th width="36">№</th>
                <th width="122">Фото</th>
                <th>Наименование</th>
                <th width="135">Цена</th>
                <th width="100">Кол-во</th>
            </tr>
            <? foreach ($items as $key => $item): ?>
                <? $goods = \Delorius\Utils\Arrays::get($item->getConfig(), 'goods'); ?>
                <tr>
                    <td class="i-center-td"><?= $key + 1 ?></td>
                    <td class="b-cart__image i-center-td">
                        <? if ($goods['image']): ?>
                            <img alt="<?= $goods['image']['name'] ?>" src="<?= $goods['image']['preview'] ?>"/>
                        <? else: ?>
                            <img alt="<?= $goods['name'] ?>" src="/source/images/no.png"/>
                        <? endif; ?>
                    </td>
                    <td class="i-middle-td">
                        <div class="b-order__name"><?= $goods['name'] ?></div>

                        <? if (count($goods['options'])): ?>
                            <? foreach ($goods['options'] as $value): ?>
                                <div class="b-order__product__options">
                                    <b><?= $value['option'] ?></b>: <?= $value['variant'] ?>
                                </div>
                            <? endforeach ?>
                        <? endif; ?>

                    </td>
                    <td class="i-middle-td b-cart__price">
                        <?= $goods['price']; ?>
                    </td>
                    <td class="i-center-td">
                        <?= $item->getQuantity() ?> <?= $goods['unit']; ?>
                    </td>
                </tr>
            <? endforeach; ?>
        </table>

        <div class="b-order__total-price"><span>Итого:</span> <?= $order_cart->getPriceTotal(); ?></div>
    </div>
</article>
