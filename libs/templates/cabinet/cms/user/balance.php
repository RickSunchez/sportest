<table class="table">
    <tr>
        <td width="300"><b style="font-size: 24px;">Баланс: <?= $balance->getPrice(null,false) ?></b></td>
        <td>
            <a class="btn btn-success" href="<?= link_to('cabinet_account')?>">Пополнить баланс</a>
            <a  class="btn btn-link open-popup" href="#alt">Альтернативные способы пополнения счета</a>
        </td>

    </tr>
</table>

<div>Кол-во операций: <?= $pagination->getItemCount(); ?> шт.</div>
<br clear="all"/>
<table class="table table-condensed table-bordered table-hover">
    <tr>
        <th>Сумма</th>
        <th>Дата</th>
        <th>Основание</th>
    </tr>
    <? foreach ($cashflow as $cash): ?>
        <tr class="type_<?= $cash->type ?>">
            <td style="text-align: right;padding-right: 5px;">
                <b><?= $cash->getNameTransactionTypes() ?> <?= $cash->value ?></b></td>
            <td style="text-align: center;width: 180px;"><?= date('d.m.Y H:i', $cash->date_cr) ?></td>
            <td><?= $cash->reason; ?></td>
        </tr>
    <? endforeach; ?>
</table>
<?= $pagination->render(); ?>

<style type="text/css">
    .type_1 {
        background-color: #DFF0D8;
    }

    .type_2 {
        background-color: #F2DEDE;
    }
</style>

<div id="alt" class="b-popup mfp-hide">
    <h3>Альтернативные способы пополнения счета</h3>
    <p>Вы можете перевести необходиму сумму на Яндекс кошелек: <b><?= $config['yandex']['receiver']?></b> через <a target="_blank" href="https://money.yandex.ru/doc.xml?id=522781">терминалы</a> вашего города или в любом отделение Евросети.</p>
    <p>Вам необходимо будет сохранить чек и через <a href="<?= link_to('cabinet_help_desk_list');?>">тех.поддержку</a> сообщить: время, сумму и идентификатор перевода.</p>
    <p>После проверки перечисленая сумму будет зачислена на Ваш счет</p>
</div>