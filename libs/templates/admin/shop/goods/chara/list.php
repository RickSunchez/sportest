<div ng-controller="CharaListCtrl" ng-init='init()'>
    <div class="pull-left">
        <div class="btn-group">
            <a title="Добавить характеристику" class="btn btn-primary btn-xs"
               href="<?= link_to('admin_chara', array('action' => 'add')) ?>">
                <i class="glyphicon glyphicon-plus"></i> Добавить характеристику
            </a>
            <a title="Добавить группу" class="btn btn-info btn-xs"
               href="<?= link_to('admin_chara', array('action' => 'listGroup')) ?>">
                <i class="glyphicon glyphicon-plus"></i> Добавить группу
            </a>
        </div>
    </div>
    <div class="pull-right">
        <div class="btn-group ">
            <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown"
                    aria-haspopup="true" aria-expanded="false">
                <i class="glyphicon glyphicon-cloud-download"></i>
            </button>
            <ul class="dropdown-menu dropdown-menu-right">
                <li><a href="<?= link_to('admin_chara', array('action' => 'loadChara')) ?>">Скачать характеристики</a>
                </li>
                <li><a href="<?= link_to('admin_chara', array('action' => 'loadValue')) ?>">Скачать значения
                        характеристик</a></li>
            </ul>
        </div>
    </div>
    <br/>
    <br/>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th>Название</th>
            <th width="250">Группа</th>
            <th width="80">Приоритет</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in chara">
            <td>{{item.character_id}}</td>
            <td>
                <a href="<?= link_to('admin_chara', array('action' => 'edit')) ?>?id={{item.character_id}}">{{item.name}}</a>
                <span class="text-preview" ng-if="item.info">({{item.info}})</span></td>
            <td>
                <select ng-model="item.group_id" class="form-control select_value" data-value="{{item.group_id}}"
                        ng-change="editGroup(item)">
                    <option value="0">Без группы</option>
                    <option ng-repeat="g in groups" value="{{g.group_id}}">{{g.name}}</option>
                </select>
            </td>
            <td>
                <div class="pos fl_r" style="padding: 0 0 0 10px;">
                    <input ng-model="item.pos" style="width: 30px; text-align: center;" ng-blur="change_pos(item)"/>
                </div>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_chara', array('action' => 'edit')) ?>?id={{item.character_id}}"><i
                                        class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.character_id)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>


</div>

<style>
    .text-preview {
        font-size: 12px;
        color: #cccccc;
        text-transform: lowercase;
    }
</style>

<script type="text/javascript">
    window._chara = <?= $chara ? \Delorius\Utils\Json::encode((array)$chara) : '[]' ?>;
    window._groups = <?= $groups ? \Delorius\Utils\Json::encode((array)$groups) : '[]' ?>;
</script>


