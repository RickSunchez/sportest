<div ng-controller="UserListCtrl" ng-init="init()">
    <div class="clearfix btn-group">
        <a class="btn btn-success btn-xs" href="<?= link_to('admin_user', array('action' => 'add')) ?>"
           title="Добавить пользователя">
            <i class="glyphicon glyphicon-plus"></i> <i class="glyphicon glyphicon-user"></i>
        </a>
        <button title="Поиск" ng-click="form_search = 1" type="button" class="btn btn-info btn-xs"><i class="glyphicon glyphicon-search"></i></button>
    </div>
    <form class="well" role="form " style="width: 400px;margin-top: 40px;"  ng-show="form_search" >
        <fieldset>
            <legend>Поиск</legend>
            <div class="form-group">
                <label for="inputactive">Статус:</label>
                <select ng-model="form.active" class="form-control" id="inputactive">
                    <option value="" >все</option>
                    <option value="1" >активные</option>
                    <option value="0" >забаненные</option>
                </select>
            </div>
            <div class="form-group">
                <label for="inputuserid">ID пользователя</label>
                <input ng-model="form.id" class="form-control" id="inputuserid" placeholder="ID пользователя">
            </div>
            <div class="form-group">
                <label for="inputemail">E-mail</label>
                <input ng-model="form.email" class="form-control" id="inputemail" placeholder="Почта пользователя">
                <p class="help-block">Возможные варианты указания значения: mail@main.ru, mail%, %in@mail.ru, %@mail%</p>
            </div>
            <div class="form-group">
                <label for="inputip">Role</label>
                <input ng-model="form.role" class="form-control" id="inputrole" placeholder="Роль пользователя">
                <p class="help-block">Возможные варианты указания значения: registered, register%, %registered, %registered%</p>
            </div>
            <div class="form-group">
                <label for="inputip">IP</label>
                <input ng-model="form.ip" class="form-control" id="inputip" placeholder="IP адрес пользователя пользователя">
                <p class="help-block">Возможные варианты указания значения: 127.0.0.1, 127%, %127, %127%</p>
            </div>


            <button ng-click="search()" type="button" class="btn btn-success">Искать</button>
            <button ng-click="cancel()" type="button" class="btn btn-default">Отмена</button>
        </fieldset>
    </form>

    <br clear="all"/><br/>
    <div>Кол-во пользователей: <?= $pagination->getItemCount(); ?> шт.</div>
    <br clear="all"/>
    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th ng-if="attr_name.length>0">Прочее</th>
            <th>Заходил</th>
            <th>IP</th>
            <th>Role</th>
            <th>Login</th>
            <th>Email</th>
            <th ng-if="balance_isset">Баланс</th>
            <th width="50">Статус</th>

            <th width="20" style="text-align: center;">#</th>
        </tr>
        <tr ng-repeat="item in users">
                <td>{{item.user_id}}</td>
                <td ng-if="attr_name.length>0">
                    <div ng-repeat="attr in attr_name">
                        {{attr.name}}:
                        <strong>{{user_attrs[item.user_id][attr.id].value}}</strong>
                    </div>
                </td>
                <td>{{item.last_logged_in}}</td>
                <td>{{item.ip}}</td>
                <td>{{item.role}}</td>
                <td>{{item.login}}</td>
                <td>{{item.email}}</td>
                <td ng-if="balance_isset" >{{getBalance(item.user_id)}}</td>
                <td style="text-align: center;">
                    <i ng-show="item.active == 1" class="glyphicon glyphicon-ok"></i>
                    <i ng-show="item.active == 0"  class="glyphicon glyphicon-remove"></i>
                </td>

                <td>

                    <div class="btn-group">
                        <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="glyphicon glyphicon-cog"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li>
                                <a title="Редактировать"
                                   href="<?= link_to('admin_user', array('action' => 'edit')) ?>?id={{item.user_id}}">
                                    <i class="glyphicon glyphicon-pencil"></i> Редактировать
                                </a>
                            </li>
                            <li>
                                <a title="Авторизоваться под пользователем"
                                   href="javascript:{}" ng-click="login(item.user_id)" >
                                    <i class="glyphicon glyphicon-log-in"></i> Авторизоваться
                                </a>
                            </li>
                            <li ng-if="balance_isset" class="divider"></li>
                            <li ng-if="balance_isset">
                                <a title="Пополнить баланс"
                                   href="<?= link_to('admin_user',array('action'=>'balance'))?>?id={{item.user_id}}">
                                    <i class="fa fa-rub"></i> Баланс {{getBalance(item.user_id)}}
                                </a>
                            </li>
                            <li class="divider"></li>
                            <li>
                                <a title="Выслать новый пароль напочту"
                                   href="javascript:{}" ng-click="changePassword(item.user_id)" >
                                    <i class="glyphicon glyphicon-envelope"></i> Выслать новый пароль
                                </a>
                            </li>
                            <li>
                                <a ng-show="item.active == 1"  title="Забанить" href="javascript:void(0);" ng-click="banned(item.user_id)" >
                                    <i class="glyphicon glyphicon-remove"></i> Забанить
                                </a>

                                <a ng-show="item.active == 0"  title="Разбарить" ng-click="unbanned(item.user_id)"  href="javascript:void(0);">
                                    <i class="glyphicon glyphicon-ok"></i> Разбанить
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
    window._users = <?= $users ? \Delorius\Utils\Json::encode((array)$users): '[]'?>;
    window._form = <?= $get ? \Delorius\Utils\Json::encode((array)$get): '{}'?>;
    window._attr_name = <?= $attr_name ? \Delorius\Utils\Json::encode((array)$attr_name): '{}'?>;
    window._user_attrs = <?= $user_attrs ? \Delorius\Utils\Json::encode((array)$user_attrs): '{}'?>;
    window._balance = <?= $balance ? \Delorius\Utils\Json::encode((array)$balance): '[]'?>;
    window._balance_isset = <?= $balance_isset ? 'true': 'false'?>;
</script>