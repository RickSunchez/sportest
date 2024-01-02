<div ng-controller="YmlListCtrl" ng-init='init()'>


    <div class="clearfix btn-group ">
        <a title="Добвить коллекцию" class="btn btn-success btn-xs"
           href="<?= link_to('admin_yml', array('action' => 'add')) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
        <a title="Очистить" class="btn btn-warning btn-xs" href="javascript:;" ng-click="clean()">
            <i class="glyphicon glyphicon-trash"></i>
        </a>
    </div>


    <br/>
    <br/>

    <div>
        <div>Кол-во yml файлов: <?= count($yml); ?></div>
    </div>
    <br/>
    <table class="table table-condensed table-bordered table-hover table-middle">
        <tr>
            <th class="i-center-td" width="20">ID</th>
            <th class="i-center-td" width="100">Статус</th>
            <th>Файл</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in yml">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-center-td">
                <span ng-if="item.exist == true">{{item.exist_date}}</span>
                <span ng-if="item.exist == false">Не создан</span>
            </td>
            <td>
                <a href="<?= link_to('admin_yml', array('action' => 'edit')) ?>?id={{item.id}}">
                    {{item.path}}
                </a>

                <span style="font-size: 12px;color: #ccc;" ng-show="item.utm">{{item.utm}}</span>

            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li ng-if="item.exist == true" >
                            <a tabindex="-1" target="_blank" href="{{item.path}}" ng-click="generation(item.id)">
                                <i class="glyphicon glyphicon-new-window"></i> Открыть файл
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="generation(item.id)">
                                <i class="glyphicon glyphicon-cog"></i> Сгенерировать файл
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="javascript:;" ng-click="deleteFile(item.id)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить файл
                            </a>
                        </li>
                        <li class="divider"></li>
                        <li>
                            <a href="<?= link_to('admin_yml', array('action' => 'edit')) ?>?id={{item.id}}"><i
                                        class="glyphicon glyphicon-edit"></i> Редактировать</a>
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
    window._yml = <?= $yml ? \Delorius\Utils\Json::encode((array)$yml) : '[]'?>;
</script>