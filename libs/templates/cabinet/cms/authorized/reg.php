<br/><br/><br/>
<div class="row" ng-controller="RegCtrl" ng-init="init()">
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
                            <label class="col-sm-3 control-label" for="email">E-mail</label>

                            <div class="col-sm-9">
                                <input id="email" type="email" name="email" ng-model="form.email" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="password1" class="required col-sm-3 control-label">Пароль</label>

                            <div class="col-sm-9">
                                <input id="password1" type="password" ng-model="form.password1" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group required">
                            <label for="password2" class="required col-sm-3 control-label">Поавторите пароль</label>

                            <div class="col-sm-9">
                                <input id="password2" type="password" ng-model="form.password2" class="form-control"/>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                <input type="button" value="Зарегистрироваться" class="btn-lg btn-default btn" ng-click="reg()">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= link_to('user_login',array('action'=>'auth'))?>" class="forgot-popup" class="btn">Вход</a>
                            </div>
                        </div>
                        <div class="form-group">
                            <div class="col-sm-offset-3 col-sm-9">
                                При регистрации вы автоматически соглашаетесь с <a target="_blank" href="[[page:8]]">пользовательским соглашением</a>.
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>