<div ng-controller="AddHelpCtrl" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_help_im', array('action' => 'list')); ?>" class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Задача</h1>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label class="col-sm-2 control-label">Тип задачи</label>

            <div class="col-sm-10">
                <p class="form-control-static"></p>

                <div class="btn-group">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        {{task.type_name}} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li ng-repeat="s in type">
                            <a class="i-cursor-pointer form-control-feedback" ng-click="re_type(s.id)">{{s.name}}</a>
                        </li>
                    </ul>
                </div>
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
            <label class="col-sm-2 control-label" for="short_title">Email:</label>

            <div class="col-sm-10">
                <input auto-complete ng-url="<?= link_to('admin_help_im_data', array('action' => 'user'))?>"  type="text" id="short_title" ng-model="task.email" ng-change="change(task.email);" class="form-control"
                       placeholder="Начните вводить Email пользователя"/>
            </div>

        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="short_title">Тема</label>

            <div class="col-sm-10">
                <input type="text" id="subject" ng-model="task.subject" class="form-control"
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
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>

    </form>


</div>

<script type="text/javascript">
    window._task = <?= $review? \Delorius\Utils\Json::encode((array)$task): '{type_name:"Выберите",status_name:"Выберите"}'?>;
    window._type = <?= $type? \Delorius\Utils\Json::encode($type): '[]'?>;
    window._status = <?= $status? \Delorius\Utils\Json::encode($status): '[]'?>;
</script>