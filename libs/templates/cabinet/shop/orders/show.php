<div class="b-order">
    <div>
        <a href="<?= link_to('cabinet_order', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <h1 class="b-order__title">Данные по заказу № <span><?= $order->getNumber() ?></span></h1>

    <p><span>Дата оформления:</span> <?= \Delorius\Core\DateTime::dateFormat($order->date_cr, true); ?></p>

    <p><span>Статус заказа:</span> <?= $order->getStatusName(); ?></p>

    <? if (!$order->paid): ?>
        <?= $order_cart->getPayment()->render() ?>
    <? endif; ?>

    <div class="b-order__contacts">
        <div class="name">Контактные данные</div>
        <p>
            <? foreach ($order->getOptions() as $opt): ?>
                <span><?= $opt->name ?>:</span> <?= $opt->value ?> <br/>
            <? endforeach ?>
        </p>
    </div>

    <table class="table table-bordered table-cart-goods">
        <tr>
            <th width="36">№</th>
            <th width="60">Фото</th>
            <th>Наименование</th>
            <th class="i-center-td" width="135">Цена</th>
            <th class="i-center-td" width="100">Кол-во</th>
        </tr>
        <? foreach ($items as $key => $item): ?>
            <? $goods = \Delorius\Utils\Arrays::get($item->getConfig(), 'goods'); ?>
            <tr>
                <td class="i-center-td"><?= $key + 1 ?></td>
                <td class="b-order__image i-center-td">
                    <? if ($goods['image']): ?>
                        <img alt="<?= $goods['image']['name'] ?>" src="<?= $goods['image']['preview'] ?>"/>
                    <? else: ?>
                        <img alt="<?= $goods['name'] ?>" src="/source/images/no.png"/>
                    <? endif; ?>
                </td>
                <td>
                    <div class="b-order__name"><?= $goods['name'] ?></div>
                    <? if (count($goods['options'])): ?>
                        <? foreach ($goods['options'] as $value): ?>
                            <div class="b-order__product__options">
                                <b><?= $value['option'] ?></b>: <?= $value['variant'] ?>
                            </div>
                        <? endforeach ?>
                    <? endif; ?>

                </td>
                <td class="i-center-td b-order__price">
                    <?= $goods['price']; ?>
                </td>
                <td class="i-center-td">
                    <?= $item->getQuantity() ?> <?= $goods['unit']; ?>
                </td>
            </tr>
        <? endforeach; ?>
    </table>

    <? if ($order_cart->getDelivery()->isActive()): ?>
        <p><b>Доставка</b>: <?= $order_cart->getDelivery()->getName() ?></p>
        <p>Примечание: <?= $order_cart->getDelivery()->getDesc() ?></p>
    <? endif; ?>

    <div class="b-order__total-price"><span>Итого:</span> <?= $order_cart->getPriceTotal(); ?></div>


</div>

<style type="text/css">

    .b-order__name {
        font-size: 16px;
    }

    .b-order__image img {
        width: 100%;
    }

    .b-order__price {
        font-size: 16px;
    }

    .b-order__total-price {
        text-align: right;
        font-size: 30px;
        font-weight: bold;
    }

    .b-order__total-price span {
        font-size: 16px;
        font-weight: normal;
    }

    .b-order__contacts {
        margin-bottom: 20px;;
    }

    .b-order__contacts .name {
        text-decoration: underline;
        font-weight: bold;
    }
</style>
