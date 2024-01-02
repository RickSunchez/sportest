<div ng-controller="UnitGoodsListCtrl" ng-init="init()">

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th>Название</th>
            <th width="100" >abbr</th>
            <th width="50" >pos</th>
            <th width="20"></th>
        </tr>
        <tr>
            <td></td>
            <td><input name="name" ng-model="form.name" class="form-control" /></td>
            <td><input name="abbr" ng-model="form.abbr" class="form-control" /></td>
            <td><input name="pos" ng-model="form.pos" class="form-control text-center" /></td>
            <td>
                <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()" href="javascript:void(0);" >
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </td>
        </tr>
        <tr ng-repeat="item in units">
            <td>{{item.unit_id}}</td>
            <td><input name="name" ng-model="item.name" class="form-control" ng-blur="edit(item)" /></td>
            <td><input name="abbr" ng-model="item.abbr" class="form-control " ng-blur="edit(item)"  /></td>
            <td class="text-center"><input name="pos" ng-model="item.pos" class="form-control text-center " ng-blur="edit(item)"  /></td>
            <td>
                <a title="Удалить" class="btn btn-xs btn-danger" ng-click="delete(item);" href="javascript:void(0);" >
                    <i class="glyphicon glyphicon-remove"></i>
                </a>
            </td>
        </tr>
    </table>

</div>
<script type="text/javascript">
    window._units = <?= $units? \Delorius\Utils\Json::encode((array)$units): '[]' ?>;
</script>