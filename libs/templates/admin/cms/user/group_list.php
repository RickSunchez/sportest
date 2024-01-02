<div ng-controller="GroupUserCtrl" ng-init="init()">

    <div class="form-group">
        <a class="btn btn-primary btn-xs" href="<?= link_to('admin_attr', array('action' => 'edit')); ?>">
            <i class="glyphicon glyphicon-plus"></i> Добавить Группу
        </a>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <thead>
        <th width="20" >ID</th>
        <th>Название группы</th>
        <th width="20">#</th>
        </thead>
        <tbody>
        <tr ng-repeat="item in groups">
            <td>{{item.group_id}}</td>
            <td>{{item.name}}</td>
            <td>
                <div class="btn-group fl_r">
                    <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li><a href="<?= link_to('admin_attr', array('action' => 'edit')); ?>?id={{item.group_id}}">
                                <span class="glyphicon glyphicon-cog"></span>
                                Редактирова группу
                            </a></li>
                        <li><a tabindex="-1" href="#" ng-click="delete(item.group_id)">
                                <i class="glyphicon glyphicon-trash"></i>
                                Удалить
                            </a></li>
                    </ul>
                </div>


            </td>
        </tr>
        </tbody>
    </table>

</div>

<script type="text/javascript">
    window._groups = <?= $groups ? \Delorius\Utils\Json::encode($groups) : ' {} ' ;?>;
</script>