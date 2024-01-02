<div class="b-stats-order">
    <h1>Статистика по заказам</h1>


    <form class="well form-horizontal" method="get" action="">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="value"><br/>Отчет показать</label>

            <div class="col-sm-2">
                с <input type="text" id="date_timepicker_start" name="start" class="form-control"
                         placeholder="__-__-____" value="<?= $get['start'] ?>" required/>
            </div>
            <div class="col-sm-2">
                по <input type="text" id="date_timepicker_end" name="end" class="form-control"
                          placeholder="__-__-____" value="<?= $get['end'] ?>" required/>
            </div>
            <div class="col-sm-5">
                <br/>
                <input type="email" name="email" class="form-control" value="<?= $get['email'] ?>"
                       placeholder="E-mail клиента"/>
            </div>
        </div>

        <button class="btn btn-info" type="submit">Показать</button>
    </form>


    <? if (count($stats)): ?>
        <? $count; ?>
        <? $value; ?>
        <table class="table table-condensed table-bordered table-hover table-middle table-edit">
            <tr>
                <th width="150">Статус</th>
                <th width="50">Кол-во</th>
                <th width="200" style="text-align: right">Общая сумма</th>
                <th></th>
            </tr>
            <? foreach ($stats as $id => $data): ?>
                <? $count += $data['count']; ?>
                <? $value += $data['value']; ?>
                <tr>
                    <td><? $s = \Shop\Store\Helper\OrderHelper::getStatusById($id);
                        echo $s['name']; ?></td>
                    <td style="text-align: center;"><?= $data['count']; ?></td>
                    <td style="text-align: right;"><?= DI()->getService('currency')->format($data['value'], SYSTEM_CURRENCY) ?></td>
                </tr>
            <? endforeach ?>

            <tr class="success">
                <td>Итого:</td>
                <td style="text-align: center;"><?= $count ?></td>
                <td style="text-align: right;"><?= DI()->getService('currency')->format($value, SYSTEM_CURRENCY) ?></td>
            </tr>
        </table>

    <? endif ?>


</div>

<script type="text/javascript">


    $(function () {
        $('#date_timepicker_start').datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({
                    maxDate: $('#date_timepicker_end').val() ? $('#date_timepicker_end').val() : false
                })
            },
            timepicker: false
        });
        $('#date_timepicker_end').datetimepicker({
            format: 'd-m-Y',
            onShow: function (ct) {
                this.setOptions({
                    minDate: $('#date_timepicker_start').val() ? $('#date_timepicker_start').val() : false
                })
            },
            timepicker: false
        });
    });
</script>


