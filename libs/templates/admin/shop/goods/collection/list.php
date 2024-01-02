<div ng-controller="CollectionProductListCtrl" ng-init="init(<?= $type_id ?>)">

    <h2>Группы товаров</h2>

    <form action="" method="get">
        <table class="table table-form table-condensed ">
            <tr>
                <td class="i-center-td" width="50">Поиск</td>
                <td><input name="name" class="form-control"
                           placeholder="Введите название группы товаров"
                           value="<?= $get['name'] ?>"/>
                </td>
                <td width="20" class="i-center-td">
                    <button class="btn btn-xs btn-info"><i class="glyphicon glyphicon-search"></i></button>
                </td>
            </tr>
        </table>
    </form>
    <br/>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th>Название группы</th>
            <th width="50" class="i-center-td"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i></th>
            <th width="20"></th>
        </tr>
        <tr>
            <td></td>
            <td><input ng-model="form.name" class="form-control"
                       placeholder="Введите название новой группы товаров"/></td>
            <td></td>
            <td class="i-center-td">
                <a title="Добавить" class="btn btn-xs btn-success" ng-click="add()" href="javascript:;">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </td>
        </tr>
        <tr ng-repeat="item in collections">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-middle-td ">
                <a href="<?= link_to('admin_product_collection', array('action' => 'edit')) ?>?id={{item.id}}">{{item.name}}</a>
            </td>
            <td class="i-center-td">
                <input name="pos" ng-model="item.pos" class="form-control text-center " ng-blur="edit(item)"/>
            </td>
            <td class="i-center-td">
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_product_collection', array('action' => 'edit')) ?>?id={{item.id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.id);">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <?= $pagination ?>

</div>
<script type="text/javascript">
    window._collections = <?= $collections ? \Delorius\Utils\Json::encode((array)$collections) : '[]' ?>;
</script>