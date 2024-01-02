<article class="b-page__show">
    <header class="b-page__header">
        <h1 class="b-order-title">Данные по заказу № <?= $order->getNumber() ?></h1>
        <time datetime="<?= date('Y-m-d', $order->date_cr) ?>">
            Дата оформления: <?= \Delorius\Core\DateTime::dateFormat($order->date_cr, true); ?>
        </time>
        <p><b>Статус заказа:</b> <?= $order->getStatusName(); ?></p>
        <? if (!$order->paid): ?>
            <?= $order_cart->getPayment()->render() ?>
        <? endif; ?>
    </header>

    <div class="b-text b-page-show__text">

        <div class="b-order__contacts b-contacts">
            <h2>Контактные данные</h2>
            <? foreach ($order->getOptions() as $opt): ?>
                <? if ($opt->code != 'type'): ?>
                    <div class="value">
                        <span><?= $opt->name ?>:</span> <?= $opt->value ?>
                    </div>
                <? endif; ?>
            <? endforeach ?>
        </div>
        <br/>

        <div class="table-responsive">

            <table class="table table-bordered">
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
                            <h2 class="b-order__name"><?= $goods['name'] ?></h2>

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
        </div>

    </div>
</article>
