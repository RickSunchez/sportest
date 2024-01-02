<br/><br/><br/>
<div class="row" ng-controller="AuthCtrl" ng-init="init()">
    <div class="col-md-offset-3 col-md-5">
        <div class="tab-content tab-content__login">
            <div class="tab-pane fade in active" id="singin">
                <form class="form-horizontal" role="form">
                    <fieldset>

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
                                &nbsp;&nbsp;&nbsp;<a href="<?= link_to('user_login',array('action'=>'remind'))?>" class="forgot-popup" class="btn">Забыл
                                    пароль? </a>
                                <br /><br />
                                <a href="<?= link_to('user_login',array('action'=>'reg'))?>" class="forgot-popup" class="btn"><i class="glyphicon glyphicon-user"></i> Регистрация пользователя</a>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>