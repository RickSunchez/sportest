<div ng-controller="HelpDeskCtrl" ng-init="init()">

    <h1>Заявка</h1>

    <div style="padding: 20px 0;">
        <a ng-hide="show" href="javascript:{}" class="btn btn-xs btn-info" ng-click="show = 1" >Создать заявку</a>
        <form class="form-horizontal well" role="form" ng-show="show">
            <div class="form-group">
                <label class="col-sm-2 control-label" for="short_title">Тема</label>

                <div class="col-sm-10">
                    <input type="text" id="subject" ng-model="form.subject" class="form-control"
                           placeholder=""/>
                </div>
            </div>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="text">Сообщение</label>

                <div class="col-sm-10">
                    <textarea ng-model="form.text" id="text" name="text" class="form-control" rows="3"></textarea>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" ng-click="send()" class="btn btn-info">Отправить</button>
                </div>
            </div>
        </form>
    </div>
</div>

<div ng-controller="HelpDeskListCtrl" ng-init="init()">

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th>Тема</th>
            <th width="20">Сообщений</th>
            <th width="20">Тип</th>
            <th width="20">Статус</th>
            <th width="20">Создано</th>
            <th width="20">Изменено</th>
        </tr>
        <tr ng-repeat="item in tasks" ng-class="{not_read:item.read_user == 0}">
            <td><a href="<?= link_to('cabinet_help_desk_show') ?>?id={{item.task_id}}"> {{item.subject}}</a></td>
            <td style="text-align: center">{{item.count_msg}}</td>
            <td style="white-space: nowrap">{{item.type_name}}</td>
            <td style="white-space: nowrap">{{item.status_name}}</td>
            <td style="white-space: nowrap">{{item.created}}</td>
            <td style="white-space: nowrap">{{item.updated}}</td>
        </tr>
    </table>
</div>
<script type="application/javascript">
    window._tasks = <?= $task ? \Delorius\Utils\Json::encode((array)$task): '[]' ?>;
</script>

