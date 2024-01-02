<div ng-controller="TagsListCtrl" ng-init="init()">

    <h2>Теги на сайте</h2>

    <form class="form-inline well well-lg">
        <div class="form-group">
            <label for="id">Тег: </label>
            <input ng-model="get.name" type="text" class="form-control" id="id" placeholder="Название"
                   style="width:200px !important;margin-right: 10px;margin-left: 10px;">
        </div>
        <div class="form-group" style="margin-right: 20px;">
            <label for="table_id">Типы: </label>
            <select ui-select2 name="table_id" ng-model="get.table_id" style="width: 200px;margin-left: 10px;">
                <option value="">Все типы</option>
                <option value="{{key}}"
                        ng-repeat="(key, value) in tables">{{value}}
                </option>
            </select>
        </div>
        <button ng-click="search()" type="button" class="btn btn-success">Искать</button>
        <button style="margin-left: 20px;" ng-click="cancel()" type="button" class="btn btn-default">Отмена</button>
    </form>
    <br/>
    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Tag</th>
            <th>Название</th>
            <th width="100">Тип</th>
            <th class="i-center-td" width="60"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i></th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in tags">
            <td class="i-center-td">{{item.tag_id}}</td>
            <td class="i-center-td">
                <span ng-if="item.status == 1">
                    <i ng-click="status(item,0)"
                       class="glyphicon glyphicon-eye-open"
                       style="cursor: pointer;color: green;"></i>
                </span>
                <span ng-if="item.status == 0">
                    <i ng-click="status(item,1)"
                       class="glyphicon glyphicon-eye-close"
                       style="cursor: pointer;"></i>
                </span>
            </td>
            <td class="i-middle-td">
                <a href="<?= link_to('admin_tags', array('action' => 'edit')) ?>?id={{item.tag_id}}">{{item.name}}</a>
            </td>
            <td class="i-middle-td">
                {{item.show}}
            </td>
            <td class="i-middle-td">
                <a href="?table_name={{item.target_name}}">{{item.target_name}}</a>
            </td>
            <td class="i-center-td">
                <input ng-model="item.pos" style="width: 100%;text-align: center;" ng-blur="save(item)"/>
            </td>
            <td class="i-center-td">
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_tags', array('action' => 'edit')) ?>?id={{item.tag_id}}"><i
                                        class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="delete(item.tag_id);">
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
    window._tags = <?= $tags ? \Delorius\Utils\Json::encode((array)$tags) : '[]' ?>;
    window._tables = <?= $tables ? \Delorius\Utils\Json::encode($tables) : '[]' ?>;
    window._get = <?= $get ? \Delorius\Utils\Json::encode((array)$get) : '{}' ?>;
</script>