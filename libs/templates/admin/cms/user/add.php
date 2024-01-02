<div ng-controller="UserAddController" ng-init="init()">
    <a href="<?= link_to('admin_user', array('action' => 'list')); ?>" class="btn btn-xs btn-info">Назад</a>
    <br/><br/>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#user" data-toggle="tab">Основные данные</a></li>
        <li><a href="#attribute" data-toggle="tab">Атрибуты</a></li>
    </ul>


    <form class="well form-horizontal">

        <div class="tab-content">
            <div class="tab-pane active" id="user">
                <fieldset>
                    <legend>Редактирования пользователя</legend>

                    <div class="alert alert-error" ng-if="errors.length">
                        <ul>
                            <li ng-repeat="error in errors">{{error}}</li>
                        </ul>
                    </div>

                    <div>
                        <div class="form-group required">
                            <label class="col-sm-2 control-label">Логин</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="user.login" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group required">
                            <label class="col-sm-2 control-label">Роли</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="user.role" class="form-control"/>
                                <p class="help-block">Указать через запятую нужные роли (<a target="_blank" href="<?= link_to('admin_acl',array('action'=>'roles','type'=>\Delorius\Security\User::DEFAULT_NAMESPACE));?>">см. список</a>)</p>
                            </div>

                        </div>

                        <div class="form-group required">
                            <label class="col-sm-2 control-label">Email</label>

                            <div class="col-sm-10">
                                <input type="text" ng-model="user.email" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group required">
                            <label class="col-sm-2 control-label">Пароль</label>

                            <div class="col-sm-10">
                                <input type="password" ng-model="user.password1" class="form-control"/>
                            </div>
                        </div>

                        <div class="form-group required">
                            <label class="col-sm-2 control-label">Повторить пароль</label>

                            <div class="col-sm-10">
                                <input type="password" ng-model="user.password2" class="form-control"/>
                            </div>
                        </div>
                    </div>
                </fieldset>
            </div>
            <div class="tab-pane" id="attribute">

                <div class="form-group">
                    <label class="col-sm-2 control-label">Группа данных</label>

                    <div class="col-sm-10">
                        <select class="form-control chara_group" ng-model="changeGroup" data-value="{{user.group_id}}">
                            <option value="">Выберите группу данных</option>
                            <option ng-repeat="opt in groups" value="{{opt.group_id}}">{{opt.name}}</option>
                        </select>
                    </div>
                </div>

                <div ng-repeat="item in attr_name[changeGroup]" class="form-horizontal">
                    <div class="form-group">
                        <label class="control-label col-sm-2">
                            {{item.name}}
                        </label>

                        <div class="col-sm-10">
                            <input class="form-control" ng-model="attr_value[changeGroup][item.id].value"/>
                        </div>
                    </div>
                </div>
            </div>

        </div>

        <div class="form-group">

            <div class="col-sm-offset-2 col-sm-10">
                <button class="btn btn-primary" type="button" ng-click="save()">Готово</button>
            </div>
        </div>
    </form>

</div>

<script type="text/javascript">
    window._user =  <?= $user ? \Delorius\Utils\Json::encode((array)$user) : '{}' ;?>;
    window._groups = <?= $groups ? \Delorius\Utils\Json::encode($groups) : ' [] ' ;?>;
    window._attr_name = <?= $attr_name ? \Delorius\Utils\Json::encode($attr_name) : ' [] ' ;?>;
    window._attr_value =  <?= $attr_value  ? \Delorius\Utils\Json::encode((array)$attr_value) : '[]' ;?>;
</script>








