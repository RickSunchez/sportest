<div ng-controller="SchemaListCtrl" ng-init='init()'>

    <div class="clearfix">
        <a class="btn btn-info btn-xs"
           href="<?= link_to('admin_schema', array('action' => 'add')); ?>"
           title="Добавить схему">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br clear="all"/>


    <p>Кол-во: <?= $pagination->getItemCount() ?></p>


    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Название</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in schemes">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-center-td">
                <span ng-if="item.status == 1"><i ng-click="status(item.id,0)" class="glyphicon glyphicon-eye-open"
                                                  style="cursor: pointer;color: green;"></i></span>
                <span ng-if="item.status == 0"><i ng-click="status(item.id,1)" class="glyphicon glyphicon-eye-close"
                                                  style="cursor: pointer;"></i></span>
            </td>
            <td><a href="<?= link_to('admin_schema', array('action' => 'edit')) ?>?id={{item.id}}">{{item.name}}</a></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_schema', array('action' => 'edit')) ?>?id={{item.id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="copy(item.id)">
                                <i class="glyphicon glyphicon-copy"></i> Копирование
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.id)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>

                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <?= $pagination->render(); ?>
</div>

<script>
    window._schemes = <?= $schemes ? \Delorius\Utils\Json::encode((array)$schemes) : '[]' ?>;
</script>


