<div ng-controller="AnalyticsController" ng-init="init()">

    <div>
        <form role="form" class="well">
            <fieldset>
                <legend>Код для аналитики</legend>
            <div class="form-group">
                <label for="selectHost">Сайт: </label>
                <select ng-model="select_domain" id="selectHost" class="form-control" style="width: 300px" ng-change="selected()">
                    <option value="">-Выберите домен-</option>
                    <option ng-repeat="d in domain" value="{{d.name}}">{{d.host}}</option>
                </select>
            </div>
            <div ng-show="select">
                <div class="form-group">
                    <label for="inputFile">Code in &lt;head&gt; </label>
                    <textarea id="inputFile" class="form-control" ng-model="form.header" style="height: 400px;" ></textarea>
                </div>
                <div class="form-group">
                    <label for="inputFile">Code in &lt;body&gt;</label>
                    <textarea id="inputFile" class="form-control" ng-model="form.footer" style="height: 400px;" ></textarea>
                </div>
                <button ng-click="save()" type="submit" class="btn btn-default">Сохранить</button>
            </div>
            </fieldset>
        </form>
    </div>

</div>

<script type="text/javascript">
    window._domain = <?= $domain?\Delorius\Utils\Json::encode((array)$domain): '[]'?>;
    window._analytics = <?= $analytics?\Delorius\Utils\Json::encode((array)$analytics): '[]'?>;
</script>