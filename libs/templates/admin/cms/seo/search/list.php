<div ng-controller="SearchListCtr" ng-init='init()'>

    <br/>

    <div class="well top-border">
        <div>Кол-во запросов: <?= $pagination->getItemCount() ?></div>


        <form action="" method="get" class="b-table">
            <div class="b-table-cell" style="width: 110px;padding-right: 10px;">
                <input value="<?= $get['type'] ?>" name="type" type="text" class="form-control"
                       placeholder="Type">
            </div>
            <div class="b-table-cell">
                <input value="<?= $get['name'] ?>" name="name" type="text" class="form-control"
                       placeholder="Название шаблона">
            </div>
            <div style="width: 200px;padding-left: 10px;" class="b-table-cell">
                <div class="btn-group">
                    <button type="submit" class="btn btn-info">Найти</button>
                    <a class="btn btn-default"
                       href="<?= link_to('admin_search', array('action' => 'list')) ?>">Сбросить</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="40">Count</th>
            <th width="60">Type</th>
            <th>Шаблон</th>
            <th>Пос. запрос</th>
            <th width="200">Ред.</th>
        </tr>
        <tr ng-repeat="item in search">
            <th class="i-center-td">{{item.count}}</th>
            <th class="i-center-td">{{item.type}}</th>
            <td>
                {{item.query_str}}
            </td>
            <td>
                {{item.query}}
            </td>
            <td>{{item.edited}}</td>
        </tr>
    </table>
    <?= $pagination->render(); ?>

</div>

<script type="text/javascript">
    window._search = <?= $search? \Delorius\Utils\Json::encode((array)$search): '[]' ?>;
</script>


