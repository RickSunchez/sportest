<div ng-controller="UserEditCtrl" ng-init=' init() ' class="b-cabinet">

    <form class="form-horizontal well" role="form">
        <fieldset>
            <legend>Пользовательские данные</legend>
            <div class="form-group">
                <label for="image" class="col-sm-4 control-label">Аватар</label>

                <div class="col-sm-8">
                    <img ng-if="image.preview" ng-src="{{image.preview}}" alt="" width="100"
                         style="margin: 5px;border: 1px solid #000;"/>
                    <input id="image" type="file" ng-file-select="onFileSelect($files,user)">
                </div>
            </div>


            <div class="form-group" ng-repeat="item in attr_value">
                <label for="attr_{{item.code}}" class="col-sm-4 control-label">{{item.name}}<span
                        style="color: #ff0000;" ng-if="item.require">*</span></label>

                <div class="col-sm-8">
                    <input type="text" class="form-control" id="attr_{{item.code}}" ng-model="item.value"/>
                </div>
            </div>

            <div class="form-group">
                <label for="login" class="col-sm-4 control-label">Логин</label>

                <div class="col-sm-8">
                    <input type="text" class="form-control" id="login" ng-model="user.login"/>
                </div>
            </div>
            <div class="form-group">
                <label for="email" class="col-sm-4 control-label">Email</label>

                <div class="col-sm-8">
                    <input type="email" class="form-control" id="email" ng-model="user.email" disabled="disabled"/>
                </div>
            </div>
        </fieldset>
        <fieldset>
            <legend>Смена пароля</legend>
            <div class="form-group">
                <label for="password" class="col-sm-4 control-label">Текущий пароль</label>

                <div class="col-sm-8">
                    <input type="password" class="form-control" id="password" ng-model="user.password"
                           placeholder="Текущий пароль">
                </div>
            </div>
            <div class="form-group">
                <label for="password1" class="col-sm-4 control-label">Пароль</label>

                <div class="col-sm-8">
                    <input type="password" class="form-control" id="password1" ng-model="user.newPassword"
                           placeholder="Пароль">
                </div>
            </div>
            <div class="form-group">
                <label for="password2" class="col-sm-4 control-label">Повторите пароль</label>

                <div class="col-sm-8">
                    <input type="password" class="form-control" id="password2" ng-model="user.newPasswordVerify"
                           placeholder="Повторить пароль">
                </div>
            </div>
        </fieldset>
        <div class="form-group">
            <div class="col-sm-offset-4 col-sm-8">
                <button type="submit" class="btn btn-info" ng-click="save()">Готово</button>
            </div>
        </div>
    </form>
</div>

<script>
    window._user = <?= $user? \Delorius\Utils\Json::encode((array)$user): '{}' ?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;
    window._attr_name = <?= $attr_name? \Delorius\Utils\Json::encode((array)$attr_name): '[]' ?>;
    window._user_attrs = <?= $user_attrs? \Delorius\Utils\Json::encode((array)$user_attrs): '[]' ?>;
</script>