<div ng-controller="SubscriberListCtr" ng-init='init(<?= $subs? \Delorius\Utils\Json::encode($subs): '{}' ?>)'>
    <div>
        <form class="form-inline well" role="form">
            <fieldset>
                <legend>Форма поиска</legend>
                <div class="form-group">
                    <label class="sr-only" for="exampleInputEmail2">Email address</label>
                    <input type="text" class="form-control" id="exampleInputEmail2" placeholder="Enter email"
                           ng-model="search.email" ng-init=" search.email='<?= $get['email']; ?>' "  style="width: 500px;">
                </div>
                <button type="submit" ng-click="search_form(true)" class="btn btn-default ">Искать</button>
                <button type="submit" ng-click="search_form(false)" class="btn btn-danger ">Отмена</button>
                <button type="submit" ng-click="add(search.email)" class="btn btn-success ">Добавить</button>
            </fieldset>
        </form>
    </div>

    <br clear="all"/>

    <div>
        <div>Кол-во записей: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th>ID</th>
            <th>Email</th>
            <th>Name</th>
            <th>Status</th>
            <th>IP</th>
            <th></th>
        </tr>
        <tr ng-repeat-start="item in subs" ng-show="isNotEdit(item);">
            <td>{{item.id}}</td>
            <td>{{item.email}}</td>
            <td>{{item.name}}</td>
            <td>
                <span ng-if="item.status == 1 ">Вкл</span>
                <span ng-if="item.status == 0 ">Выкл</span>
            </td>
            <td>{{item.ip}}</td>
            <td class="i-center-td">
                <a title="Редактировать"  class="btn btn-xs btn-default" ng-click="startEdit(item)">
                    <i class="glyphicon glyphicon-pencil"></i>
                </a>
                <a title="Удалить" class="btn btn-xs btn-danger" ng-click="delete(item.id)">
                    <i class="glyphicon glyphicon-trash"></i>
                </a>
            </td>
        </tr>
        <tr ng-repeat-end ng-hide="isNotEdit(item);">
            <td>{{item.id}}</td>
            <td><input type="text" ng-model="item.email" class="form-control"/></td>
            <td><input type="text" ng-model="item.name" class="form-control"/></td>
            <td><input type="checkbox" ng-model="item.status" ng-true-value="1" ng-false-value="0"
                       class="form-control"/></td>
            <td>{{item.ip}}</td>
            <td>
                <a class="btn btn-xs btn-primary" ng-click="saveEdit(item)">Сохранить</a>
                <a class="btn btn-xs btn-default" ng-click="cancelEdit(item)">Отмена</a>
            </td>
        </tr>
    </table>

    <?= $pagination->render(); ?>

</div>

