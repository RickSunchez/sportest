<h1>Заказ</h1>

<p>Код заказа: <?= $order->getNumber() ?></p>
<p>Выбранный способ оплаты: <b><?= $basket->getPayment()->getName(); ?></b></p>

<p><b>Состав заказа:</b></p>

<table width="100%" border="0" cellpadding="0" cellspacing="0">
    <tr>
        <th style="border:1px solid #000;border-right:none;border-bottom:none;padding: 10px;" >Фото</th>
        <th align="left" style="border:1px solid #000;border-bottom:none;border-right:none;padding-left: 20px;text-align: left;">Название</th>
        <th style="border:1px solid #000;border-bottom:none;border-right:none;padding: 10px;" >Артикул</th>
        <th style="border:1px solid #000;border-bottom:none;border-right:none;padding: 10px;" >Цена</th>
        <th style="border:1px solid #000;border-bottom:none;padding: 10px;" >Кол-во</th>
    </tr>
    <? foreach ($basket->getProducts() as $key => $item): ?>
        <tr>
            <td style="border: 1px solid #000;border-right:none;" width="55" align="center" valign="middle">
                <? if ($item->image): ?>
                    <img style="width: 50px;display: block;margin: 0px auto;" alt="<?= $item->image->name ?>"
                         src="<?= \CMS\Core\Helper\Helpers::canonicalUrl($item->image->preview) ?>"/>
                <? endif ?>
            </td>
            <td style="border: 1px solid #000;border-right:none;padding-left: 20px;vertical-align: middle;">
                <div style="font-size: 14px;font-weight: bold;padding-top: 10px;"><?= $item->name ?></div>
                <? if (count($item->options)): ?>
                    <? foreach ($item->options as $value): ?>
                        <p><b><?= $value['option'] ?></b>: <?= $value['variant'] ?></p>
                    <? endforeach ?>
                <? endif; ?>
            </td>
            <td style="border: 1px solid #000;border-right:none;" width="80" align="center">
                <?= $item->article ?>
            </td>
            <td style="border: 1px solid #000;" width="80" align="right">
                <?= $item->getPrice(); ?>
            </td>
            <td style="border: 1px solid #000;text-align: center;vertical-align: middle" width="80" align="center">
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
    <p>Примечание: <?= $basket->getDelivery()->getDesc() ?></p>
<? endif; ?>

<p>
    Для того что посмотреть заказ на сайте перейтие по <a href="<?= $order->link() ?>">ссылке</a>
</p>

