<div ng-controller="HelpMessagesCtrl" ng-init="init()">


    <form class="form-horizontal well" role="form">
        <div class="form-group">
            <label class="col-sm-2 control-label">Тема</label>

            <div class="col-sm-10">
                <p class="form-control-static">{{task.subject}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Статус</label>

            <div class="col-sm-10">
                <p class="form-control-static"></p>

                <div class="btn-group">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        {{task.status_name}} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li ng-repeat="s in status">
                            <a class="i-cursor-pointer form-control-feedback" ng-click="re_status(s.id)">{{s.name}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Дата создания</label>

            <div class="col-sm-10">
                <p class="form-control-static">{{task.created}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label">Сообщение</label>

            <div class="col-sm-10">
                <div class="form-control-static" style="border: 1px solid #000;padding: 5px;" ng-bind-html="to_trusted(task.text)" ></div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Ответ</label>
            <div class="col-sm-10">
                <textarea ng-model="form.text" id="text" name="text" class="form-control" rows="3"></textarea>
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button ng-click="send()" type="button" class="btn btn-info">Ответить</button>
            </div>
        </div>
    </form>

    <br /><br />

    <div class="message" ng-repeat="item in messages">
        <div class="who">
            В {{item.created}} пишет
            <b>
                <span ng-if="item.is_admin == 1" >администрация</span>
                <span ng-if="item.is_admin == 0" >пользователь</span>
            </b>:
        </div>
        <div class="text" ng-bind-html="to_trusted(item.text)"></div>
    </div>


</div>

<script type="text/javascript">
    window._task = <?= $task? \Delorius\Utils\Json::encode($task): '{}'?>;
    window._messages = <?= $messages? \Delorius\Utils\Json::encode($messages): '[]'?>;
    window._type = <?= $type? \Delorius\Utils\Json::encode($type): '[]'?>;
    window._status = <?= $status? \Delorius\Utils\Json::encode($status): '[]'?>;
</script>