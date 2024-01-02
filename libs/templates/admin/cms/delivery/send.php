<div ng-controller="DeliverySendController" ng-init='init()'>

   <form class="form-horizontal well" role="form">

        <div class="alert alert-error" ng-if="errors.length">
            <ul>
                <li ng-repeat="error in errors">{{error}}</li>
            </ul>
        </div>

        <fieldset>
            <legend>Шаблон письма</legend>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject">E-mail</label>

                <div class="col-sm-10">
                    <input type="text" id="subject" ng-model="form.email" class="form-control"
                           placeholder="Тестовый email"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="subject">Заголовок письма</label>

                <div class="col-sm-10">
                    <input type="text" id="subject" ng-model="form.subject" class="form-control"
                           placeholder="Заголовок письма"/>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="text">Текст</label>

                <div class="col-sm-10">
                    <textarea id="message" ng-model="form.message" class="form-control"></textarea>
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" ng-click="send()" class="btn btn-primary">Отправить</button>
                </div>

            </div>
        </fieldset>
    </form>

</div>

<script type="text/javascript">
    window._form = <?= $form ? \Delorius\Utils\Json::encode((array)$form) : "{status:0}";?>;
    window._groups = <?= \Delorius\Utils\Json::encode((array)$groups);?>;
    window._group_id = <?= $group_id? (int)$group_id: 'null' ;?>;
</script>






