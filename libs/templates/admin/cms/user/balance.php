<div ng-controller="BalanceCtrl" ng-init="init()">
    <div class="clearfix btn-group">
        <a class="btn btn-success btn-xs" href="<?= link_to('admin_user', array('action' => 'list')) ?>"
           title="Назад к списку">
            Назад
        </a>

    </div>

    <div>
        <h2>Пользователь: {{user.email}}</h2>
        <h4>Баланс: {{balance.value}}</h4>
        <form class="form-horizontal" >
            <fieldset>
                <legend>Операция по балансу</legend>
                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">Сумма:</label>

                    <div class="col-sm-2">
                        <select class="form-control" ng-model="form.type" id="type">
                            <option value="<?= \Shop\Store\Entity\Cashflow::PLUS?>">Приход</option>
                            <option value="<?= \Shop\Store\Entity\Cashflow::MINUS?>">Расход</option>
                        </select>
                    </div>
                </div>
                <div class="form-group">
                    <label for="summer" class="col-sm-2 control-label">Сумма:</label>

                    <div class="col-sm-2">
                        <input ng-model="form.value" parser-int  type="text" class="form-control" id="summer"/>
                    </div>
                </div>
                <div class="form-group">
                    <label for="reason" class="col-sm-2 control-label">Основание:</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="form.reason"  class="form-control" id="reason"/>
                        <p class="help-block">Обязательно укажите основания для операции</p>
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button type="button"  ng-click="send()" class="btn btn-default">Выполнить</button>
                    </div>
                </div>
            </fieldset>
        </form>
    </div>

    <br clear="all"/><br/>

    <div>Кол-во операций: <?= $pagination->getItemCount(); ?> шт.</div>
    <br clear="all"/>
    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="80">Тип</th>
            <th>Сумма</th>
            <th>Дата</th>
            <th>Основание</th>
            <th>IP</th>
        </tr>
        <tr class="type_{{item.type}}" ng-repeat="item in cashflow">
            <td>{{item.cash_id}}</td>
            <td>{{item.type_name}}</td>
            <td><b>{{item.value}}</b></td>
            <td>{{item.created}}</td>
            <td>{{item.reason}}</td>
            <td>{{item.ip}}</td>
        </tr>
    </table>
    <?= $pagination->render(); ?>

</div>

<script type="text/javascript">
    window._user = <?= $user ? \Delorius\Utils\Json::encode((array)$user): '{}'?>;
    window._balance = <?= $balance ? \Delorius\Utils\Json::encode((array)$balance): '[]'?>;
    window._cashflow = <?= $cashflow ? \Delorius\Utils\Json::encode((array)$cashflow): '[]'?>;
</script>

<style type="text/css">
    .type_1{
        background-color: #DFF0D8;
    }
    .type_2{
        background-color: #F2DEDE;
    }
</style>