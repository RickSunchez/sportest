<div ng-controller="CurrencyListCtrl" ng-init='init()'>

    <div class="clearfix">
        <a title="Добавить валюту" class="btn btn-success btn-xs" href="<?= link_to('admin_currency', array('action' => 'add')) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
        <a title="Обновить данные по ЦБ" class="btn btn-info btn-xs" href="javascript:;" ng-click="refresh()">
            <i class="glyphicon glyphicon-repeat"></i>
        </a>
    </div>
    <br clear="all" />
    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="100">Код ISO</th>
            <th width="100">Номинал</th>
            <th width="100">Курс (руб.)</th>
            <th>Название</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in currency">
            <td class="i-center-td" >{{item.currency_id}}</td>
            <td class="i-center-td">{{item.code}}</td>
            <td><input type="text" ng-model="item.nominal"  class="form-control text-right"  ng-blur="edit(item)"/></td>
            <td><input type="text" ng-model="item.value" class="form-control  text-right" ng-blur="edit(item)" /></td>
            <td>{{item.name}} <span ng-if="isDefault(item.code)">(по умолчанию)</span></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_currency', array('action' => 'edit')) ?>?id={{item.currency_id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.currency_id)" >
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>


</div>

<script type="text/javascript">
    window._currency = <?= $currency? \Delorius\Utils\Json::encode((array)$currency): '[]' ?>;
    window._config = <?= $config? \Delorius\Utils\Json::encode((array)$config): '[]' ?>;
</script>


