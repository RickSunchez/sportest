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


        <? if (false): ?>
            <div class="b-order__contacts b-contacts">
                <h2>Контактные данные</h2>
                <? foreach ($order->getOptions() as $opt): ?>
                    <div class="value">
                        <?= $opt['name'] ?>: <?= $opt['value'] ?>
                    </div>
                <? endforeach ?>
            </div>
        <? endif; ?>


        <table class="table table-bordered table-cart-goods">
            <tr>
                <th width="36">№</th>
                <th width="50">Фото</th>
                <th>Наименование</th>
                <th width="100">Кол-во</th>
                <th width="135">Цена</th>
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
                    <td class="i-center-td">
                        <?= $item->getQuantity() ?> <?= $goods['unit']; ?>
                    </td>
                    <td class="i-middle-td b-cart__price">
                        <?= $goods['price']; ?>
                    </td>
                </tr>
            <? endforeach; ?>
            <? if ($order_cart->getDiscount()->isValid()): ?>
                <tr>
                    <td class="i-middle-td text-right" colspan="4">
                        <div class="b-order__name">
                            <?= $order_cart->getDiscount()->getLabel() ?>
                        </div>
                    </td>
                    <td class="i-middle-td b-cart__price"><?= $order_cart->getDiscount()->getPercent() ?>%</td>
                </tr>
            <? endif ?>
            <? if ($order_cart->getDelivery()->isActive()): ?>
                <tr>
                    <td class="i-middle-td text-right" colspan="4">
                        <div class="b-order__name">
                            <?= $order_cart->getDelivery()->getName() ?>
                            <? if ($order_cart->getDelivery()->getDesc()): ?>
                                <span>
                                 - <?= $order_cart->getDelivery()->getDesc() ?>
                            </span>
                            <? endif; ?>
                        </div>

                    </td>
                    <td class="i-middle-td b-cart__price"><?= $order_cart->getDelivery()->getPrice() ?></td>
                </tr>
            <? endif; ?>
            <tr>
                <td class="i-middle-td text-right" colspan="4">
                    <div class="b-order__name">
                        Итого:
                    </div>

                </td>
                <td class="i-middle-td b-cart__price"><?= $order_cart->getPriceTotal(); ?></td>
            </tr>
        </table>
    </div>
</article>
