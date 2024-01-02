<div ng-controller="SubscriptionListCtr" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_subscription', array('action' => 'add')) ?>">Добавить
            подписку</a>
    </div>
    <br clear="all"/>

    <div>
        <div>Кол-во записей: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="50">Тип</th>
            <th width="20">Кол-во</th>
            <th>Название</th>
            <th>Адрес формы</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in subs">
            <td>{{item.group_id}}</td>
            <td>{{item.type_name}}</td>
            <td align="center">{{item.count}}</td>
            <td>{{item.name}}</td>
            <td><a href="{{getUrl(item.url)}}" target="_blank">{{getUrl(item.url)}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_subscription', array('action' => 'edit')) ?>?id={{item.group_id}}"><i
                                    class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a onclick="return confirm('Вы действительно хотите удалить подписку?');"
                               href="<?= link_to('admin_subscription', array('action' => 'delete')) ?>?id={{item.group_id}}"><i
                                    class="glyphicon glyphicon-trash"></i> Удалить</a>
                        </li>
                        <li role="presentation" class="divider"></li>
                        <li ng-if="item.type == 'sub' ">
                            <a href="<?= link_to('admin_delivery', array('action' => 'send')) ?>?id={{item.group_id}}"><i
                                    class="glyphicon glyphicon-envelope"></i> Быстрая рассылка</a>
                        </li>

                        <li ng-if="item.type == 'bid' ">
                            <a href="<?= link_to('admin_subscription', array('action' => 'bid')) ?>?id={{item.group_id}}"><i
                                    class="glyphicon glyphicon-pencil"></i> Заявки</a>
                        </li>

                    </ul>
                </div>
            </td>
        </tr>
    </table>
    <?= $pagination->render(); ?>

</div>

<script type="application/javascript">
    window._subs = <?= $subs? \Delorius\Utils\Json::encode($subs): '{}' ?>;
</script>


