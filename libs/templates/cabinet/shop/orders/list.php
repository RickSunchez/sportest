<div ng-controller="CabinetOrdersListCtrl" ng-init='init()'>

    <form action="<?= link_to('cabinet_order',array('action'=>'list'));?>" method="get" style="width: 500px;margin-top: 40px;" class="well">
        <fieldset>
            <legend>Поиск заказа по коду</legend>
            <div class="form-group">
                <input name="number" type="text" value="<?= $get['number'] ?>" style="width: 250px;">
                <button style="margin: 5px 15px" type="submit" class="btn btn-success">Искать</button>
                <button style="margin: 5px 15px"
                        onclick="window.location = '<?= link_to('cabinet_order',array('action'=>'list')) ?>';return false;"
                        class="btn btn-success">Сброс
                </button>
            </div>
        </fieldset>
    </form>


    <p>Кол-во заказов: <?= $pagination->getItemCount() ?></p>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="10">Code</th>
            <th width="150">Статус</th>
            <th width="150">Сумма</th>
            <th width="150">Создан</th>
            <th width="150">Изменен</th>
        </tr>
        <tr class="status_{{item.status}}" ng-repeat="item in orders" ng-class="{not_registered:item.user_id == 0}">
            <td><a href="<?= link_to('cabinet_order',array('action'=>'view')) ?>?id={{item.order_id}}">{{item.number}}</a></td>
            <td>{{item.status_name}}</td>
            <td ng-bind-html="as_html(item.price)"></td>
            <td>{{item.created}}</td>
            <td>{{item.updated}}</td>
        </tr>
    </table>
    <?= $pagination?>
</div>

<script type="text/javascript">
    window._orders = <?= $orders? \Delorius\Utils\Json::encode((array)$orders): '[]' ?>;
</script>