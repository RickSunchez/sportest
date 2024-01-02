<div ng-controller="CallbackListCtr" ng-init='init()'>
    <div>
        <div>Кол-во заявок: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="150">Создана</th>
            <th>Данные</th>
            <th>Обработал</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in callback" ng-class="{success:item.date_finished>0}">
            <td>{{item.created}}</td>
            <td>
                <b>{{item.subject}}</b>

                <div ng-repeat="i in item.form">
                    {{i.name}}: {{i.value}}
                </div>
            </td>
            <td>
                <div>
                    <div ng-if="item.user_id>0">Логин: {{getLogin(item.user_id)}}</div>
                    <div ng-if="item.date_finished>0">Дата: {{item.finished}}</div>
                </div>
                <div ng-if="item.user_id == 0">
                    <a class="btn btn-success btn-xs" href="javascript:void(0)" ng-click="treat(item.callback_id)">Потвердить
                        обработку</a>
                </div>
            </td>
            <td>
                <a title="Удалить" class="btn btn-xs btn-danger" href="javascript:void(0)"
                   ng-click="delete(item.callback_id)">
                    <i class="glyphicon glyphicon-trash"></i>
                </a>
            </td>
        </tr>
    </table>
    <?= $pagination->render(); ?>

</div>

<script type="text/javascript">
    window._callback = <?= $callback? \Delorius\Utils\Json::encode((array)$callback): '[]' ?>;
    window._users = <?= $users? \Delorius\Utils\Json::encode((array)$users): '[]' ?>;
</script>


