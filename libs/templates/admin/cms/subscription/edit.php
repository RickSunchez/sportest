<div ng-controller="SubscriptionEditController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_subscription', array('action' => 'list')); ?>"
           class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <form class="form-horizontal well" role="form">

        <div class="alert alert-danger" ng-if="errors.length">
            <ul>
                <li ng-repeat="error in errors">{{error}}</li>
            </ul>
        </div>

        <fieldset>
            <legend>Подписка</legend>

            <div class="form-group" ng-hide="sub.group_id > 0">
                <label for="inputPassword" class="col-sm-2 control-label">Тип</label>

                <div class="col-sm-10">
                    <div class="radio">
                        <label>
                            <input type="radio" name="optionsRadios" ng-model="sub.type" value="sub">
                            Подписная страницы
                        </label>
                    </div>
                    <div class="radio">
                        <label>
                            <input type="radio" name="optionsRadios" ng-model="sub.type" value="bid">
                            Станица для заявок
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group" ng-show="sub.group_id > 0">
                <label for="inputPassword" class="col-sm-2 control-label">Тип</label>

                <div class="col-sm-10">
                    <p class="form-control-static">
                        <span ng-show="sub.type == 'bid' " >Станица для заявок</span>
                        <span ng-show="sub.type == 'sub' " >Подписная страницы</span>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">Название</label>

                <div class="col-sm-10">
                    <input type="text" id="title" ng-model="sub.name" class="form-control"
                           placeholder="Название подписки"/>
                </div>
            </div>

        </fieldset>

        <fieldset>
            <legend>Форма</legend>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="title">Поля</label>

                <div class="col-sm-10">

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" ng-model="sub.is_name"
                                   ng-true-value="1" ng-false-value="0">
                            Имя
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" ng-model="sub.is_phone"
                                   ng-true-value="1" ng-false-value="0">
                            Телефон
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" ng-model="sub.is_email"
                                   ng-true-value="1" ng-false-value="0">
                            Email
                        </label>
                    </div>

                    <div class="checkbox">
                        <label>
                            <input type="checkbox" ng-model="sub.is_comment"
                                   ng-true-value="1" ng-false-value="0">
                            Коментарий
                        </label>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="city">Город</label>

                <div class="col-sm-10">
                    <input type="text" id="city" ng-model="config.city" class="form-control"
                           placeholder="Название города"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="name_m">Мероприятие</label>

                <div class="col-sm-10">
                    <input type="text" id="name_m" ng-model="config.name" class="form-control"
                           placeholder="Название мероприятия"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="date">Дата проведения</label>

                <div class="col-sm-10">
                    <input type="text" id="date" ng-model="config.date" class="form-control"
                           placeholder="Напиши дату проведения"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="about">Описание мероприятия</label>

                <div class="col-sm-10">
                    <textarea name="text" id="about" ng-model="config.about" class="form-control"
                              placeholder="Описание мероприятия"></textarea>
                </div>
            </div>


        </fieldset>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>
</div>
<script type="text/javascript">
    window._sub = <?= $sub ? \Delorius\Utils\Json::encode((array)$sub) : 'null'?>;
    window._config = <?= $config ? \Delorius\Utils\Json::encode((array)$config) : 'null'?>;
</script>







