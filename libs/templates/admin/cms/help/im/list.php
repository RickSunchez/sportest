<div ng-controller="HelpImListCtr" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_help_im', array('action' => 'add')) ?>" title="Добавить задачу">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br clear="all"/>



    <form class="form-inline well"
          method="get"
          action="<?= link_to('admin_help_im',array('action'=>'list'))?>"
          style="width: 420px;">
        <fieldset>
            <legend>Форма поиска</legend>
            <div class="form-group">
                <label class="sr-only" for="exampleInputEmail2">Email address</label>
                <input type="text" class="form-control" id="exampleInputEmail2" placeholder="Enter email"
                      value="<?= $get['email']; ?>" name="email"  style="width: 300px;">
            </div>
            <button type="submit" class="btn btn-default">Искать</button>
        </fieldset>
    </form>


    <div>
        <div>Кол-во записей: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th>Тип</th>
            <th>Статус</th>
            <th>Кол-во</th>
            <th>Название</th>
            <th>Пользователь</th>
            <th>Читал</th>
            <th width="180">Дата создания</th>
            <th width="180">Дата изменения</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in tasks" ng-class="{not_read:item.read_admin == 0}">
            <td><a href="<?= link_to('admin_help_im', array('action' => 'edit')) ?>?id={{item.task_id}}" >#{{item.task_id}}</a></td>
            <td>{{item.type_name}}</td>
            <td>{{item.status_name}}</td>
            <td style="text-align: center">{{item.count_msg}}</td>
            <td>{{item.subject}}</td>
            <td>
                <a href="<?= link_to('admin_help_im',array('action'=>'list'))?>?user_id={{item.user_id}}" >{{getUsersss(item.user_id)}}</a>
            </td>
            <td>
                <span ng-if="item.read_user == 1" >Читал</span><span ng-if="item.read_user == 0" >Не&nbsp;читал</span>
            </td>
            <td>{{item.created}}</td>
            <td>{{item.updated}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_help_im', array('action' => 'edit')) ?>?id={{item.task_id}}">
                                <i class="glyphicon glyphicon-pencil"></i> Ответить
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.task_id)" >
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a target="_blank" href="<?= link_to('admin_user', array('action' => 'list')) ?>?id={{item.user_id}}">
                                <i class="glyphicon glyphicon-user"></i> Перейти к пользователю
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
    window._tasks = <?= $tasks? \Delorius\Utils\Json::encode((array)$tasks): '[]' ?>;
    window._users = <?= $users? \Delorius\Utils\Json::encode((array)$users): '[]' ?>;
</script>


