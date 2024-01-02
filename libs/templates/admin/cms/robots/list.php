<div ng-controller="RobotsController" ng-init="init()">


    <div>
        <form role="form" class="well">
            <fieldset>
                <legend>Редактирования robots.txt</legend>
            <div class="form-group">
                <label for="selectHost">Сайт: </label>
                <select ng-model="select_domain" id="selectHost" class="form-control" style="width: 300px" ng-change="selected()">
                    <option value="">-Выберите домен-</option>
                    <option ng-repeat="d in domain" value="{{d.name}}">{{d.host}}</option>
                </select>
            </div>
            <div ng-show="select">
                <div class="form-group">
                    <label for="inputFile">robots.txt</label>
                    <textarea id="inputFile" class="form-control lined " ng-model="form.value" style="height: 400px;" ></textarea>
                </div>
                <button ng-click="save()" type="submit" class="btn btn-default">Сохранить</button>
            </div>
            </fieldset>
        </form>

    </div>


</div>

<script type="text/javascript">
    window._domain = <?= $domain?\Delorius\Utils\Json::encode((array)$domain): '[]'?>;
    window._robots = <?= $robots?\Delorius\Utils\Json::encode((array)$robots): '[]'?>;
</script>