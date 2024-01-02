<div ng-controller="OrderListCtrl" ng-init="init()">

    <div class="clearfix btn-group ">

        <a title="Статистика" class="btn btn-info btn-xs"
           href="<?= link_to('admin_order', array('action' => 'stat')) ?>">
            <i class="glyphicon glyphicon-calendar"></i> Статистика
        </a>
    </div>

    <form action="<?= link_to('admin_order', array('action' => 'list')) ?>" method="get"
          style="width: 500px;margin-top: 20px;" class="well">
        <fieldset>
            <legend>Поиск заказа по коду</legend>
            <div class="form-group">
                <input name="number" type="text" value="<?= $get['number'] ?>" style="width: 250px;">
                <button style="margin: 5px 15px" type="submit" class="btn btn-success">Искать</button>
                <button style="margin: 5px 15px"
                        onclick="window.location = '<?= link_to('admin_order', array('action' => 'list')) ?>';return false;"
                        class="btn btn-success">Сброс
                </button>
            </div>
        </fieldset>
    </form>

    <table class="table table-hover table-bordered">
        <thead>
        <tr>
            <th class="col-sm-2">код заказа</th>
            <th class="col-sm-1">Cумма</th>
            <th class="col-sm-3">Данные пользователя</th>
            <th class="col-sm-1">Дата</th>
            <th>Примечание</th>
            <th width="20" >#</th>
        </tr>
        </thead>
        <tbody>
        <tr class="status_{{item.status}}" ng-class="{not_registered:item.user_id == 0}" ng-repeat="item in orders">
            <td>
                <a href="<?= link_to('admin_order', array('action' => 'edit')) ?>?id={{item.order_id}}">
                    <i class="glyphicon glyphicon-eye-open"></i>
                    {{item.number}}
                </a>

                <div class="btn-group" style="padding-top: 10px;">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        {{item.status_name}} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li ng-repeat="s in status">
                            <a class="i-cursor-pointer form-control-feedback" ng-click="re_status(s.id,item.order_id)">{{s.name}}</a>
                        </li>
                    </ul>
                </div>

                <div style="color: red;padding-top: 20px;font-size: 12px;">
                    {{item.discount.label}}
                </div>

            </td>
            <td>
                <div style="text-align: right;" ng-bind-html="html(item.price_raw)"></div>
            </td>
            <td>
                <div>{{users[item.user_id].email}}</div>
                <div ng-repeat="opt in options[item.order_id]"><b
                        style="border-bottom:1px double #000;">{{opt.name}}:</b> {{opt.value}};
                </div>
            </td>
            <td>{{item.created}}</td>
            <td>
                <div ng-show="isNotEditNote(item)">
                    {{truncate(item.note,100)}}
                </div>
                <div ng-show="isNotEditNote(item)" style="text-align: right;">
                    <a ng-click="startEditNote(item)" title="Редактировать примечание" href="javascript:{}"
                       class="btn btn-link btn-xs"><i class="glyphicon glyphicon-pencil"></i></a>
                </div>


                <textarea ng-hide="isNotEditNote(item)" ng-model="item.note" class="form-control"></textarea>

                <div ng-hide="isNotEditNote(item)" style="text-align: right;">
                    <a ng-click="saveEditNote(item)" title="Сохранить примечание" href="javascript:{}"
                       class="btn btn-link btn-xs"><i class="glyphicon glyphicon-ok"></i></a>
                    <a ng-click="cancelEditNote()" title="Отменить изменения" href="javascript:{}"
                       class="btn btn-link btn-xs"><i class="glyphicon glyphicon-remove"></i></a>
                </div>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a title="Подробнее"
                               href="<?= link_to('admin_order', array('action' => 'edit')); ?>?id={{item.order_id}}">
                                <i class="icon-edit"></i> Подробнее
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
        </tbody>
    </table>

    <?= $pagination; ?>

</div>

<script type="text/javascript">
    window._orders = <?= $orders ? \Delorius\Utils\Json::encode($orders) : '[]' ;?>;
    window._users = <?= $users? \Delorius\Utils\Json::encode((array)$users): '[]' ?>;
    window._status = <?= $status? \Delorius\Utils\Json::encode((array)$status): '[]' ?>;
    window._options = <?= $options? \Delorius\Utils\Json::encode((array)$options): '[]' ?>;
    window._get = <?= $get? \Delorius\Utils\Json::encode((array)$get): '[]' ?>;
</script>