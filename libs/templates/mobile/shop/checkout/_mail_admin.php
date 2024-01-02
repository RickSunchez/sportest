<h1>Заказ</h1>

<p>Код заказа: <?= $order->getNumber() ?></p>
<? if ($basket->getPayment()->isActive()): ?>
    <p>Выбранный способ оплаты: <b><?= $basket->getPayment()->getName(); ?></b></p>
<? endif; ?>
<p><b>Состав заказа:</b></p>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <th align="left"
            style="border:1px solid #000;border-bottom:none;border-right:none;padding-left: 10px;text-align: left;">
            Название
        </th>
        <th style="border:1px solid #000;border-bottom:none;border-right:none;padding: 10px;">Артикул</th>
        <th style="border:1px solid #000;border-bottom:none;border-right:none;padding: 10px;">Цена</th>
        <th style="border:1px solid #000;border-bottom:none;padding: 10px;">Кол-во</th>
    </tr>
    <? foreach ($basket->getProducts() as $key => $item): ?>
        <tr>
            <td style="border: 1px solid #000;border-right:none;padding:5px 5px 5px 10px;vertical-align: middle;">
                <div style="font-size: 14px;font-weight: bold;padding-top: 10px;"><?= $item->name ?></div>
                <? if (count($item->options)): ?>
                    <? foreach ($item->options as $value): ?>
                        <p><b><?= $value['option'] ?></b>: <?= $value['variant'] ?></p>
                    <? endforeach ?>
                <? endif; ?>
            </td>
            <td style="border: 1px solid #000;border-right:none;padding: 5px;" width="80" align="center">
                <?= $item->article ?>
            </td>
            <td style="border: 1px solid #000;padding: 5px;" width="80" align="right">
                <?= $item->getPrice(); ?>
            </td>
            <td style="border: 1px solid #000;text-align: center;vertical-align: middle;padding: 5px;" width="80"
                align="center">
                <?= $basket->getQuantity($item->combination_hash) ?> <?= $item->getUnit(); ?>
            </td>

        </tr>
    <? endforeach ?>
    <tr>
        <td colspan="2">
            <? if ($basket->getDiscount()->isValid()): ?>
                <span style="color: #FF0000;"><?= $basket->getDiscount()->getLabel() ?></span>
            <? endif; ?>
        </td>
        <td style="padding-top:20px;text-align: right" colspan="3">
            Игото: <?= $basket->getPriceTotal(); ?>
        </td>
    </tr>
</table>

<? if ($basket->getDelivery()->isActive()): ?>
    <p><b>Доставка</b>: <?= $basket->getDelivery()->getName() ?></p>
    <? if ($basket->getDelivery()->getDesc()): ?>
        <p>Примечание: <?= $basket->getDelivery()->getDesc() ?></p>
    <? endif; ?>
<? endif; ?>

<p>
    Для того что посмотреть заказ на сайте перейтие по <a href="<?= $order->link() ?>">ссылке</a>
</p>

<p>
    <b>Оформлено с мобильной версии сайта.</b>
</p>

