<br/>
<div class="row" ng-controller="LoginController" ng-init="init()">
    <div class="col-md-offset-1 col-md-9">
        <!-- Nav tabs -->
        <ul class="nav nav-tabs">
            <li class="active"><a href="#singin" data-toggle="tab">Вход</a></li>
            <li><a href="#registration" data-toggle="tab">Регистрация</a></li>
        </ul>

        <!-- Tab panes -->
        <div class="tab-content tab-content__login">
            <div class="tab-pane fade in active" id="singin">
                <form class="form-horizontal" role="form">

                        <div class="alert alert-danger " ng-show="error.length">
                            <strong>Ошибка!</strong>
                            {{error}}
                        </div>

                        <div class="form-group">
                            <label class="col-sm-2 control-label" for="email">E-mail</label>

                            <div class="col-sm-10">
                                <input id="email" type="email" name="email" ng-model="form.email" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="password" class="required col-sm-2 control-label">Пароль</label>

                            <div class="col-sm-10">
                                <input id="password" type="password" ng-model="form.password" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="button" value="Вход" class="btn-lg btn-default btn" ng-click="login()">
                                <a href="<?= link_to('user_login', array('action' => 'forgot')) ?>" class="btn">Забыл
                                    пароль? </a>
                            </div>
                        </div>

                </form>
            </div>
            <div class="tab-pane fade " id="registration">
                <form class="form-horizontal" role="form">

                        <div class="alert alert-danger" ng-if="errors.length">
                            <strong>Ошибка!</strong>
                            <div ng-repeat="error in errors">{{error}}</div>
                        </div>

                        <div class="form-group">
                            <label class="col-sm-3 control-label" for="email_reg">E-mail</label>

                            <div class="col-sm-9">
                                <input id="email_reg" type="email" name="email" ng-model="form.email_reg"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="password_reg" class="required col-sm-3 control-label">Пароль</label>

                            <div class="col-sm-9">
                                <input id="password_reg" type="password" ng-model="form.password1"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="password2_reg" class="required col-sm-3 control-label">Повторить</label>

                            <div class="col-sm-9">
                                <input id="password2_reg" type="password" ng-model="form.password2"
                                       class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <input type="button" value="Зарегистрироваться" class="btn-lg btn-default btn"
                                       ng-click="reg()">
                            </div>
                        </div>
                </form>
            </div>
        </div>

    </div>

</div>