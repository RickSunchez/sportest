<br/><br/><br/>
<div class="row" ng-controller="LoginController">
    <div class="col-md-offset-3 col-md-5">

        <? if ($error): ?>
            <div class="alert alert-danger alert-dismissable">
                <button type="button" class="close" data-dismiss="alert" aria-hidden="true">&times;</button>
                <strong>Error!</strong> <?= $error ?>
            </div>
        <? endif; ?>

        <form class="form-horizontal well" role="form" method="post" action="">

            <fieldset>
                <legend>Вход в систему</legend>
                <div class="form-group">
                    <label for="inputLogin" class="col-sm-2 control-label">Логин</label>

                    <div class="col-sm-10">
                        <input ng-model="form.login" type="text" class="form-control" id="inputLogin" placeholder="Логин" name="login">
                    </div>
                </div>
                <div class="form-group">
                    <label for="inputPassword3" class="col-sm-2 control-label">Пароль</label>

                    <div class="col-sm-10">
                        <input ng-model="form.password" type="password" class="form-control" id="inputPassword3" placeholder="Пароль"
                               name="password">
                    </div>
                </div>
                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <button ng-click="login()" type="submit" class="btn btn-info">Войти</button>
                    </div>
                </div>
            </fieldset>
        </form>

    </div>
</div>

