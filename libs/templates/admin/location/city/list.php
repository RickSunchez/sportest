<div ng-controller="CitiesControllers" ng-init='init()'>

    <div class="btn-group ">
        <a title="Добвить город" class="btn btn-success btn-xs"
           href="<?= link_to('admin_city', array('action' => 'add')) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
        <a title="Показать все влюченые города" class="btn btn-info btn-xs"
           href="<?= link_to('admin_city', array('action' => 'list', 'status' => 1)) ?>">
            <i class="glyphicon glyphicon-eye-open"></i>
        </a>
        <a title="Показать все выключеные города" class="btn btn-warning btn-xs"
           href="<?= link_to('admin_city', array('action' => 'list', 'status' => 0)) ?>">
            <i class="glyphicon glyphicon-eye-close"></i>
        </a>
        <? if (isset($get['country_id'])): ?>
            <a title="Назад ко всем городам" class="btn btn-danger btn-xs"
               href="<?= link_to('admin_city', array('action' => 'list')) ?>">
                <i class="glyphicon glyphicon-arrow-left"></i> назад ко всем городам
            </a>
        <? endif; ?>
    </div>
    <div class="btn-group pull-right ">
        <a title="Включить все города" class="btn btn-primary btn-xs "
           href="<?= link_to('admin_city', array('action' => 'statusAll', 'status' => 1)) ?>">
            <i class="glyphicon glyphicon-ok-circle"></i>
        </a>
        <a title="Вылючить все города" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_city', array('action' => 'statusAll', 'status' => 0)) ?>">
            <i class="glyphicon glyphicon-remove-circle"></i>
        </a>
    </div>
    <br/>
    <br/>


    <div class="well top-border">
        <div>Кол-во городов: <?= $pagination->getItemCount() ?></div>

        <? if ($country): ?>
            <div>Cтрана: <?= $country->name ?></div>
        <? endif; ?>
        <form action="" method="get" class="b-table">
            <div class="b-table-cell"><input value="<?= $get['city'] ?>" name="city" type="text" class="form-control"
                                             placeholder="Название города">
            </div>
            <div style="width: 200px;padding-left: 10px;" class="b-table-cell">
                <div class="btn-group">
                    <button type="submit" class="btn btn-info">Найти</button>
                    <a class="btn btn-default"
                       href="<?= link_to('admin_city', array('action' => 'list')) ?>">Сбросить</a>
                </div>
            </div>
        </form>
    </div>
    <table class="table table-condensed table-bordered table-hover table-middle table-edit">
        <tr>
            <th width="20">ID</th>
            <th width="20"><i class="glyphicon glyphicon-star"></i></th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Название</th>
            <th>Страна</th>
            <th class="i-center-td" width="75"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i></th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in cities">
            <td>{{item.id}}</td>

            <td class="i-center-td">
                <span ng-if="item.main == 0">
                    <i ng-click="main(item.id,1)" class="glyphicon glyphicon-star-empty" style="cursor: pointer;"></i>
                </span>
                <span ng-if="item.main == 1">
                    <i ng-click="main(item.id,0)" class="glyphicon glyphicon-star"
                       style="cursor: pointer;color: #FF0000"></i>
                </span>
            </td>
            <td class="i-center-td">
                <span ng-if="item.status == 1">
                    <i ng-click="status(item.id,0)" class="glyphicon glyphicon-eye-open"
                       style="cursor: pointer;color: green;"></i>
                </span>
                <span ng-if="item.status == 0">
                    <i ng-click="status(item.id,1)" class="glyphicon glyphicon-eye-close"
                       style="cursor: pointer;"></i>
                </span>
            </td>
            <td>
                <a href="<?= link_to('admin_city', array('action' => 'edit')) ?>?id={{item.id}}">
                    {{item.name}}
                </a>
                ({{item.name_2}}, {{item.name_3}}, {{item.name_4}})
            </td>
            <td>
                <a href="<?= link_to('admin_city', array('action' => 'list')) ?>?country_id={{item.country_id}}">
                    {{item.country_name}}
                </a>
            </td>
            <td class="i-center-td">
                <input ng-model="item.pos" class="pos" ng-blur="change_pos(item)"/>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_city', array('action' => 'edit')) ?>?id={{item.id}}"><i
                                        class="glyphicon glyphicon-edit"></i> Редактировать</a>
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

<script type="text/javascript">
    window._cities = <?= $cities ? \Delorius\Utils\Json::encode((array)$cities) : '[]' ?>;
</script>