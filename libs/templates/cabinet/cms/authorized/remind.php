<br/><br/><br/>
<div class="row" ng-controller="RemindCtrl" >
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
                        <div class="form-group">
                            <div class="col-sm-offset-2 col-sm-10">
                                <input type="button" value="Оправить на почту" class="btn-lg btn-default btn" ng-click="remind()">
                                &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="<?= link_to('user_login',array('action'=>'auth'))?>" class="forgot-popup" class="btn">Вход</a>
                            </div>
                        </div>
                    </fieldset>
                </form>
            </div>
        </div>
    </div>
</div>