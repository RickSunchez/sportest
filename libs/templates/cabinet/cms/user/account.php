<div class="b-form" ng-controller="UserBillController">
    <form class="well form-horizontal" style="width: 500px;">
        <fieldset>
            <legend>
                Формирования счета
            </legend>
            <div class="form-group">
                <label for="input1" class="col-sm-4 control-label">Сумма</label>

                <div class="col-sm-8">
                    <input type="text" ng-model="form.value" class="form-control" id="input1" placeholder="руб.">
                </div>
            </div>
            <div class="form-group">
                <label for="input2" class="col-sm-4 control-label">Способ оплаты</label>

                <div class="col-sm-8">
                    <select ng-model="form.payment_id" class="form-control">
                        <option value="1">Яндекс.Деньги</option>
                        <option value="2">Банковская карта</option>
                    </select>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button ng-click="send()" type="button" class="btn btn-info">Создать</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>

<div>Кол-во счетов: <?= $pagination->getItemCount(); ?> шт.</div>
<br clear="all"/>
<table class="table table-condensed table-bordered table-hover">
    <tr>
        <th width="20">ID</th>
        <th width="150">Сумма</th>
        <th>Дата</th>
        <th width="100">#</th>
    </tr>
    <? foreach ($bills as $item): ?>
        <tr class="type_<?= $item->status?>">
            <td><?= $item->pk()?></td>
            <td style="text-align: right;padding-right: 5px;">
                <b><?= $item->getPrice(null, false); ?></b></td>
            <td style="text-align: center;width: 180px;"><?= date('d.m.Y H:i', $item->date_cr) ?></td>
            <td>
                <? if ($item->status == $item::STATUS_PAID): ?>
                    Оплачен
                <? else: ?>
                    <?= $item->getPayment()->render() ?>
                <?endif; ?>
            </td>
        </tr>
    <? endforeach; ?>
</table>
<?= $pagination->render(); ?>


<style type="text/css">
    .type_2 {
        background-color: #DFF0D8;
    }
</style>