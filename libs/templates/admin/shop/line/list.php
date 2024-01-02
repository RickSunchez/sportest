<div ng-controller="LineListCtrl" ng-init='init()'>


    <h2>Выборки товаров</h2>


    <table class="table table-condensed table-bordered table-hover table-middle">
        <tr>
            <th class="i-center-td" width="20">ID</th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Название</th>
            <th class="i-center-td" width="50">
                <i title="Приоритет" class="glyphicon glyphicon-sort-by-attributes-alt"></i>
            </th>
            <th width="20"></th>
        </tr>
        <tr class="active">
            <td></td>
            <td></td>
            <td><input name="name" ng-model="form.name" class="form-control"/></td>
            <td><input name="pos" ng-model="form.pos" class="form-control text-center"/></td>
            <td width="20">
                <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()" href="javascript:;">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </td>
        </tr>
        <tr ng-repeat="item in lines">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-center-td">
                <span ng-if="item.status == 1"><i ng-click="status(item.id,0)" class="glyphicon glyphicon-eye-open"
                                                  style="cursor: pointer;color: green;"></i></span>
                <span ng-if="item.status == 0"><i ng-click="status(item.id,1)" class="glyphicon glyphicon-eye-close"
                                                  style="cursor: pointer;"></i></span>
            </td>
            <td>
                <a href="<?= link_to('admin_line_product', array('action' => 'edit')) ?>?id={{item.id}}">
                    <span ng-show="item.status == 1"> {{item.name}}</span>
                    <s ng-show="item.status == 0">{{item.name}}</s>
                </a>
            </td>
            <td class="i-center-td">
                <input ng-model="item.pos" style="width: 20px; text-align: center;" ng-blur="save(item)" class="pos"/>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_line_product', array('action' => 'edit')) ?>?id={{item.id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.id)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

</div>
