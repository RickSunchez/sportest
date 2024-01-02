<div ng-controller="UserEditCtrl" ng-init='user.email = "<?= $user->email ?>" '>

    <form class="form-horizontal well col-sm-8" role="form">
        <fieldset>
            <legend>Изменить пароль</legend>
            <div class="form-group">
                <label for="email" class="col-sm-4 control-label">Email</label>

                <div class="col-sm-8">
                    <input type="email" class="form-control" id="email" ng-model="user.email" disabled="disabled" />
                </div>
            </div>
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
            <div class="form-group">
                <div class="col-sm-offset-4 col-sm-8">
                    <button type="submit" class="btn btn-info" ng-click="save()">Готово</button>
                </div>
            </div>
        </fieldset>
    </form>
</div>