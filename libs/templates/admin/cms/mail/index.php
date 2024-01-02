<div ng-controller="MailController" ng-init="init()">
    <form class="form-horizontal well" role="form" ng-show=" form ">

        <div class="form-group">
            <fieldset>
                <legend>Шаблон для групповой рассылки</legend>
                <label class="col-sm-2 control-label" for="inputEmail">Категория</label>

                <div class="col-sm-10">
                    <select ng-change="recalculate()" ng-model="currentGroup"
                            ng-options="group.name for group in groups" class="form-control">
                        <option value="">--Выберите--</option>
                    </select>
                    &nbsp&nbsp&nbsp
                    <span ng-if="is_send">Кол-во подписчиков: {{countMail}}</span>
                </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="subject">Заголовок письма</label>

            <div class="col-sm-10">
                <input type="text" id="subject" ng-model="mail.subject" class="form-control"
                       placeholder="Заголовок письма"/>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="text">Текст</label>

            <div class="col-sm-10">
                <textarea name="text" id="text" ng-model="mail.text" class="form-control"></textarea>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="send()" class="btn btn-primary">Отправить</button>
            </div>
        </div>

        </fieldset>
    </form>


    <div ng-hide=" form ">
        <h1>Кол-во подписчиков: {{countMail}}</h1>
        <div>Осталось: {{countEnd}}</div>
        <div class="progress progress-striped active">
            <div class="progress-bar" role="progressbar" aria-valuenow="45" aria-valuemin="0" aria-valuemax="100"
                 style="width: {{progress}}%">
                <span class="sr-only">{{progress}}% Complete</span>
            </div>
        </div>
        <div>Не закрывайте страницу пока не закончится процесс</div>

    </div>


</div>
<script type="text/javascript">
    window._groups = <?= \Delorius\Utils\Json::encode((array)$groups);?>;
    window._group_id = <?= $id? (int)$id: 'null' ;?>;
</script>