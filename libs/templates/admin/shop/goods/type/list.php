<div ng-controller="TypeGoodsListCtrl" ng-init="init()">
    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th>Название типа товара</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in types">
            <td>{{item.id}}</td>
            <td>{{item.name}}</td>
            <td>
                <a title="Показать товары" class="btn btn-xs btn-info" href="<?= link_to('admin_goods_type',array('action'=>'goods'));?>?id={{item.id}}" >
                    <i class="glyphicon glyphicon-eye-open"></i>
                </a>
            </td>
        </tr>
    </table>

</div>
<script type="text/javascript">
    window._types = <?= $types? \Delorius\Utils\Json::encode((array)$types): '[]' ?>;
</script>