<div ng-controller="GoodsOptionsListCtrl" ng-init='init()'>
    <div class="clearfix btn-group ">
        <a title="Назад к товару" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'edit', 'id' => $goods['goods_id'])) ?>">
            <i class="glyphicon glyphicon-arrow-left"></i> Назад к товару
        </a>

    </div>
    <a title="Комбинации опций" class="btn btn-default btn-xs pull-right"
       href="<?= link_to('admin_option', array('action' => 'combination', 'id' => $goods['goods_id'])) ?>">
        <i class="glyphicon glyphicon-th"></i> Комбинации опций
    </a>

    <h1>Опции к "<?= $goods['name'] ?>"</h1>

    <br clear="all"/>
    <a title="Добавить опций" class="btn btn-default"
       href="<?= link_to('admin_option', array('action' => 'add', 'id' => $goods['goods_id'])) ?>">
        <i class="glyphicon glyphicon-plus"></i>
    </a>

    <div class="counter">Кол-во опций: <?= count($options) ?></div>


    <table class="table table-condensed table-bordered table-hover table-middle table-edit">
        <tr>
            <th width="20">ID</th>
            <th>Название</th>
            <th width="140" class="i-center-td">Тип</th>
            <th width="80" class="i-center-td"><i class="glyphicon glyphicon-asterisk" title="Обезатльная"></i></th>
            <th width="80" class="i-center-td"><i class="glyphicon glyphicon-refresh" title="Комбинация"></i></th>
            <th class="i-center-td" width="75"><i class="glyphicon glyphicon-sort-by-attributes-alt" title="Приоритет"></i></th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in options">
            <td>{{item.id}}</td>
            <td>
                <a href="<?= link_to('admin_option', array('action' => 'edit')) ?>?id={{item.id}}">
                    <span ng-show="item.status == 1"> {{item.name}}</span>
                    <s ng-show="item.status == 0">{{item.name}}</s>
                </a>
            </td>
            <td class="i-center-td">{{item.type_name}}</td>
            <td class="i-center-td">
                <span ng-if="item.required == 1">Да</span>
                <span ng-if="item.required == 0">Нет</span>
            </td>
            <td class="i-center-td">
                <span ng-if="item.inventory == 1">Да</span>
                <span ng-if="item.inventory == 0">Нет</span>
            </td>
            <td class="i-center-td">
                <input ng-model="item.pos" class="pos" ng-blur="change(item)"/>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_option', array('action' => 'edit')) ?>?id={{item.id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.id)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

</div>

<script type="text/javascript">
    window._options = <?= $options? \Delorius\Utils\Json::encode((array)$options): '[]'?>;
</script>