<div ng-controller="CategoryFilterListController" ng-init='init(<?= $cid ?>)'>
    <div class="clearfix btn-group ">
        <a title="Добвить подборку" class="btn btn-success btn-xs"
           href="<?= link_to('admin_category_filter', array('action' => 'add', 'cid' => $cid)) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br/>
    <br/>

    <table class="table table-condensed table-bordered table-hover table-middle table-edit">
        <tr>
            <th width="20">ID</th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Название</th>
            <th>ЧПУ</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in filters">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-center-td">
                <span ng-if="item.status == 1">
                    <i ng-click="status(item.id,0)"
                       class="glyphicon glyphicon-eye-open"
                       style="cursor: pointer;color: green;"></i>
                </span>
                <span ng-if="item.status == 0">
                    <i ng-click="status(item.id,1)"
                       class="glyphicon glyphicon-eye-close"
                       style="cursor: pointer;"></i>
                </span>
            </td>
            <td>
                <a href="<?= link_to('admin_category_filter', array('action' => 'edit')) ?>?id={{item.id}}">
                    <span ng-show="item.status == 1"> {{item.name}}</span>
                    <s ng-show="item.status == 0">{{item.name}}</s>
                </a>
            </td>
            <td>
                {{item.url}}
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_category_filter', array('action' => 'edit')) ?>?id={{item.id}}"><i
                                        class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.id)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>


</div>