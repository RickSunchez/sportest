<div ng-controller="RedirectListCtrl" ng-init="init()">

    <h2>Список перенаправлений</h2>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th colspan="2">Старая ссылка</th>
            <th colspan="2">Новая ссылка</th>
            <th class="i-center-td" width="40"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i></th>
            <th width="20"></th>
        </tr>
        <tr>
            <td width="100">
                <select ui-select2 class="form-control" ng-model="form.type_url">
                    <option value="{{p.id}}" ng-repeat="p in paths">{{p.name}}</option>
                </select>
            </td>
            <td><input ng-model="form.url" class="form-control"/></td>
            <td width="100">
                <select ui-select2 class="form-control" ng-model="form.type_move">
                    <option value="{{m.id}}" ng-repeat="m in moves">{{m.name}}</option>
                </select>
            </td>
            <td><input ng-model="form.move" class="form-control"/></td>
            <td class="i-center-td" width="40"><input class="pos40" ng-model="item.pos"/></td>
            <td class="i-center-td">
                <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()" href="javascript:;">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </td>
        </tr>
        <tr ng-repeat="item in redirects">
            <td width="100">
                <select ui-select2 class="form-control" ng-model="item.type_url" ng-change="edit(item)">
                    <option value="{{p.id}}" ng-repeat="p in paths">{{p.name}}</option>
                </select>
            </td>
            <td><input name="name" ng-model="item.url" class="form-control" ng-blur="edit(item)"/></td>
            <td width="100">
                <select ui-select2 class="form-control" ng-model="item.type_move" ng-change="edit(item)">
                    <option value="{{m.id}}" ng-repeat="m in moves">{{m.name}}</option>
                </select>
            </td>
            <td><input name="pos" ng-model="item.move" class="form-control" ng-blur="edit(item)" /></td>
            <td class="i-center-td" width="40"><input class="pos40" ng-model="item.pos" ng-blur="edit(item)"/></td>
            <td class="i-center-td">
                <a title="Удалить" class="btn btn-xs btn-danger" ng-click="delete(item);" href="javascript:;">
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
            </td>
        </tr>
    </table>

</div>
<script type="text/javascript">
    window._redirects = <?= $redirects ? \Delorius\Utils\Json::encode((array)$redirects) : '[]' ?>;
    window._moves = <?= $moves ? \Delorius\Utils\Json::encode((array)$moves) : '[]'?>;
    window._paths = <?= $paths ? \Delorius\Utils\Json::encode((array)$paths) : '[]'?>;
</script>
<style>
    .pos40 {
        width: 40px;
        text-align: center;
    }
</style>