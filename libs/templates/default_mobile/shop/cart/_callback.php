<h1>Быстрый заказ</h1>

<p> Дата заказа: <?= date('d.m.Y H:i') ?></p>
<? if (count($form)): ?>
    <h2>Данные пользователя:</h2>

    <? foreach ($form as $name => $value): ?>
        <p><b><?= _t('Shop:Store', 'form_' . $name) ?></b>: <?= $value ?></p>
    <? endforeach ?>
<? endif; ?>
<h2>Данные заказа:</h2>
<table width="100%" cellpadding="0" cellspacing="0" border="0">
    <tr>
        <th style="border:1px solid #000;border-right:none;border-bottom:none;padding: 10px" >Фото</th>
        <th align="left" style="border:1px solid #000;border-bottom:none;border-right:none;padding-left: 20px;text-align: left;">Название</th>
        <th style="border:1px solid #000;border-bottom:none;border-right:none;padding: 10px" >Артикул</th>
        <th style="border:1px solid #000;border-bottom:none;padding: 10px" >Цена</th>
    </tr>
    <tr>
        <td style="border: 1px solid #000;border-right:none;" width="55" align="center" valign="middle">
            <? if ($goods->image): ?>
                <img style="width: 50px;" alt="<?= $goods->image->name ?>"
                     src="<?= \CMS\Core\Helper\Helpers::canonicalUrl($goods->image->preview) ?>"/>
            <? endif ?>
        </td>
        <td style="border: 1px solid #000;border-right:none;padding-left: 20px;vertical-align: middle;">
            <div style="font-size: 16px;font-weight: bold;padding-top: 10px;"><?= $goods->name ?></div>
            <? if (count($goods->options)): ?>
                <? foreach ($goods->options as $value): ?>
                    <p><b><?= $value['option'] ?></b>: <?= $value['variant'] ?></p>
                <? endforeach ?>
            <? endif; ?>
        </td>
        <td style="border: 1px solid #000;border-right:none;" width="80" align="center">
            <?= $goods->article ?>
        </td>
        <td style="border: 1px solid #000;" width="80" align="right">
            <?= $goods->getPrice(); ?>
        </td>
    </tr>

    <? if (count($additions)): ?>
        <tr>
            <td colspan="4" style="border-bottom: 1px solid #000;">
                <h2>Дополнения</h2>
            </td>
        </tr>
        <? foreach ($additions as $addition): ?>
            <tr>
                <td style="border: 1px solid #000;border-right:none;border-top: none;" width="55" align="center" valign="middle">
                    <? if ($addition->image): ?>
                        <img style="width: 50px;"
                             alt="<?= $addition->image->name ?>"
                             src="<?= \CMS\Core\Helper\Helpers::canonicalUrl($addition->image->preview) ?>"/>
                    <? endif ?>
                </td>
                <td style="border: 1px solid #000;border-right:none;border-top: none;padding-left: 20px;vertical-align: middle;">
                    <div style="font-size: 14px;font-weight: bold;"><?= $addition->name ?></div>
                    <? if (count($addition->options)): ?>
                        <? foreach ($addition->options as $value): ?>
                            <p><b><?= $value['option'] ?></b>: <?= $value['variant'] ?></p>
                        <? endforeach ?>
                    <? endif; ?>
                </td>
                <td style="border: 1px solid #000;border-right:none;border-top: none;" width="80" align="center">
                    <?= $addition->article ?>
                </td>
                <td style="border: 1px solid #000;border-top: none;" width="80" align="right">
                    <?= $addition->getPrice(); ?>
                </td>
            </tr>
        <? endforeach; ?>
    <? endif; ?>
</table>

<p><b>Мобильная версия сайта</b></p>