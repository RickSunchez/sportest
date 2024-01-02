<div ng-controller="DeliveryAddController" ng-init='init()'>

    <a href="<?= link_to('admin_delivery', array('action' => 'list')); ?>" class="btn btn-xs btn-info">Назад</a>
    <br/ ><br/ >
    <form class="form-horizontal well" role="form">

        <div class="alert alert-error" ng-if="errors.length">
            <ul>
                <li ng-repeat="error in errors">{{error}}</li>
            </ul>
        </div>

        <fieldset>
            <legend>Шаблон письма</legend>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject">Статус:</label>

                <div class="col-sm-10">
                    <label class="checkbox">
                        <input type="checkbox" ng-model="delivery.status" ng-true-value="1" ng-false-value="0"/> <span
                            ng-if="delivery.status == 1 ">Вкл</span><span ng-if="delivery.status == 0 ">Выкл</span>
                    </label>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject">Группа подписчиков:</label>

                <div class="col-sm-10">
                    <span class="nullable">
                        <select ng-model="currentGroup"
                                ng-options="group.name for group in groups" class="form-control">
                            <option value="">-По всей базе-</option>
                        </select>
                    </span>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject">Заголовок письма</label>

                <div class="col-sm-10">
                    <input type="text" id="subject" ng-model="delivery.subject" class="form-control"
                           placeholder="Заголовок письма"/>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="text">Текст</label>

                <div class="col-sm-10">
                    <div style="padding: 10px 0;">Доступные шаблоны: [name],[hash],[email]</div>
                    <textarea id="message" ng-model="delivery.message" class="form-control"></textarea>
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" ng-click="save()" class="btn btn-primary">Готово</button>
                    <button ng-if="delivery.delivery_id > 0" type="submit" ng-click="reset()" class="btn btn-danger">
                        Запустить заново
                    </button>
                </div>

            </div>
        </fieldset>
        <fieldset>
            <legend>Тестовое письмо на указаный email:</legend>
            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject">Тестовый email</label>

                <div class="col-sm-10">
                    <input type="text" id="subject" ng-model="test_email" class="form-control"
                           placeholder="Тестовый email"/>
                </div>
            </div>
            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" ng-click="test()" class="btn btn-warning">Отправить</button>
                </div>
            </div>
        </fieldset>


    </form>

</div>

<script type="text/javascript">
    window._delivery = <?= $delivery ? \Delorius\Utils\Json::encode((array)$delivery) : "{status:0}";?>;
    window._groups = <?= \Delorius\Utils\Json::encode((array)$groups);?>;
    window._group_id = <?= $group_id? (int)$group_id: 'null' ;?>;
</script>






