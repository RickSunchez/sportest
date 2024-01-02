<div ng-controller="PollListController" ng-init='init()'>

    <div class="btn-group clearfix">
        <a title="Добавить опрос" class="btn btn-primary btn-xs"
           href="<?= link_to('admin_poll', array('action' => 'add')) ?>">
            <i class="glyphicon glyphicon-plus"></i> Добавить опрос
        </a>
    </div>

    <br/>
    <br/>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Орпос</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in polls">
            <td class="i-center-td">{{item.poll_id}}</td>
            <td class="i-center-td">
                <span ng-show="item.status == 0">
                    <i ng-click="status(item.poll_id,1)" class="glyphicon glyphicon-eye-close"
                       style="cursor: pointer;"></i>
                </span>
                 <span ng-show="item.status == 1">
                    <i ng-click="status(item.poll_id,0)" class="glyphicon glyphicon-eye-open"
                       style="cursor: pointer;color: #FF0000"></i>
                </span>
            </td>
            <td>
                <div><b>{{item.name}}</b></div>
                <small><i>{{item.text}}</i></small>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_poll', array('action' => 'edit')) ?>?id={{item.poll_id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.poll_id)">
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
    window._polls = <?= $polls? \Delorius\Utils\Json::encode((array)$polls): '[]' ?>;
    window._get = <?= $get? \Delorius\Utils\Json::encode((array)$get): '[]' ?>;
</script>


