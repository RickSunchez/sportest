<div ng-controller="BannersListCtrl" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_banner', array('action' => 'add')) ?>" title="Добавить баннер">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br clear="all"/>

    <form action="<?= link_to('admin_banner',array('action'=>'list')) ?>" method="get" style="width: 500px;" class="well">
        <fieldset>
            <legend>Укажите код баннера</legend>
            <div class="form-group">
                <input name="code" type="text" value="<?= $get['code'] ?>" style="width: 250px;">
                <button style="margin: 5px 15px" type="submit" class="btn btn-success">Искать</button>
                <button style="margin: 5px 15px"
                        onclick="window.location = '<?= link_to('admin_banner',array('action'=>'list')) ?>';return false;"
                        class="btn btn-success">Сброс
                </button>
            </div>
        </fieldset>
    </form>


    <div>
        <div>Кол-во баннеров: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th width="50">Статус</th>
            <th width="50">Тип</th>
            <th width="50">Код</th>
            <th>Название</th>
            <th style="text-align: center;"><i title="Показы" class="glyphicon glyphicon-eye-open"></i></th>
            <th style="text-align: center;"><i title="Клики" class="glyphicon glyphicon-share-alt"></i></th>
            <th width="150">Действует до</th>
            <th width="50">Поз.</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in banners">
            <td>{{item.banner_id}}</td>
            <td>
                <span ng-if="item.status == 0" >
                    <i ng-click="status(item.banner_id,1)" class="glyphicon glyphicon-eye-close" style="cursor: pointer;"></i>
                </span>
                <span ng-if="item.status == 1" >
                    <i ng-click="status(item.banner_id,0)" class="glyphicon glyphicon-eye-open" style="cursor: pointer;color: green;"></i>
                </span>
            </td>
            <td>
                <span ng-if="item.status == 1" >ВКЛ</span>
                <span ng-if="item.status == 0" >ВЫКЛ</span>
            </td>
            <td>{{item.type_name}}</td>
            <td>{{item.code}}</td>
            <td><a href="<?= link_to('admin_banner', array('action' => 'edit')) ?>?id={{item.banner_id}}">{{item.name}}</a></td>
            <td style="text-align: center;">{{item.visit}}</td>
            <td style="text-align: center;">{{item.click}}</td>
            <td>{{item.date_show_up}}</td>
            <td><input style="text-align: center;" type="text" ng-model="item.pos" class="form-control col-sm-2" ng-change="changePos(item)" /></td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_banner', array('action' => 'edit')) ?>?id={{item.banner_id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.banner_id)" >
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

<script type="application/javascript">
    window._banners = <?= $banners? \Delorius\Utils\Json::encode((array)$banners): '[]' ?>;
</script>


