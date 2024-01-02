<div ng-controller="ProviderGoodsListCtrl" ng-init="init()">

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>          
            <th>Название</th>
            <th width="50">pos</th>
            <th width="20"></th>
        </tr>
        <tr>
            <td></td>           
            <td><input name="name" ng-model="form.name" class="form-control"/></td>
            <td><input name="pos" ng-model="form.pos" class="form-control "/></td>
            <td>
                <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()" href="javascript:;">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </td>
        </tr>
        <tr ng-repeat="item in providers">
            <td class="i-center-td">{{item.id}}</td>            
            <td class="i-center-td">
                <input name="name" ng-model="item.name" class="form-control" ng-blur="edit(item)"/>
            </td>
            <td class="i-center-td">
                <input name="pos" ng-model="item.pos" class="form-control text-center " ng-blur="edit(item)"/>
            </td>
            <td class="i-center-td">
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_provider', array('action' => 'edit')) ?>?id={{item.id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.id);">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <?= $pagination ?>

</div>
<script type="text/javascript">
    window._providers = <?= $providers? \Delorius\Utils\Json::encode((array)$providers): '[]' ?>;    
</script>