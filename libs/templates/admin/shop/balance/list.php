<div ng-controller="BalanceCtrl" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_user', array('action' => 'add')) ?>" title="Добавить новость">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br clear="all"/>


    <div>
        <div>Кол-во статей: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="140">Статус</th>
            <th>Название</th>
            <th width="140">Дата</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in articles">
            <td>{{item.id}}</td>
            <td>
                <span ng-if="item.status == 1" >ВКЛ</span>
                <span ng-if="item.status == 0" >ВЫКЛ</span>
            </td>
            <td>{{item.name}}</td>
            <td>{{item.created}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="fa fa-list"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_article', array('action' => 'edit')) ?>?id={{item.id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.id)" >
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

<script type="text/javascript">
    window._articles = <?= $articles? \Delorius\Utils\Json::encode((array)$articles): '{}' ?>;
</script>


