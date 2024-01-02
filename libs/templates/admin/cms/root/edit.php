<div ng-controller="RootController" ng-init='init(<?= $root?\Delorius\Utils\Json::encode((array)$root): '{}'?>)'>
    <a href="<?= link_to('admin_root', array('action' => 'list')); ?>" class="btn btn-xs btn-info">Назад</a>
    <br/ ><br/ >


    <form class="form-horizontal well" role="form">
        <div class="form-group">
            <label for="inputLogin3" class="col-sm-2 control-label">Логин</label>

            <div class="col-sm-10">
                <input type="Login" class="form-control" id="inputLogin3" ng-model="root.login" placeholder="Login">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Пароль</label>

            <div class="col-sm-10">
                <input type="password" class="form-control" id="inputPassword3" ng-model="root.newPassword" placeholder="Пароль">
            </div>
        </div>
        <div class="form-group">
            <label for="inputPassword3" class="col-sm-2 control-label">Повторите пароль</label>

            <div class="col-sm-10">
                <input type="password" class="form-control" id="inputPassword3" ng-model="root.newPasswordVerify" placeholder="Повторить пароль">
            </div>
        </div>
        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" class="btn btn-info" ng-click="save()">Готово</button>
            </div>
        </div>
    </form>
</div>





