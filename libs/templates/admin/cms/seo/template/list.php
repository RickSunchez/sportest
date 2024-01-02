<div ng-controller="TemplateListCtr" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_tmp', array('action' => 'add')) ?>"
           title="Добавить шаблон">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
<br/>
    <div class="well top-border">
        <div>Кол-во шаблонов: <?= $pagination->getItemCount() ?></div>


        <form action="" method="get" class="b-table">
            <div class="b-table-cell">
                <input value="<?= $get['name'] ?>" name="name" type="text" class="form-control"
                                             placeholder="Название шаблона">
            </div>
            <div style="width: 200px;padding-left: 10px;" class="b-table-cell">
                <div class="btn-group">
                    <button type="submit" class="btn btn-info">Найти</button>
                    <a class="btn btn-default"
                       href="<?= link_to('admin_tmp', array('action' => 'list')) ?>">Сбросить</a>
                </div>
            </div>
        </form>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th>Название</th>
            <th width="200">Ред.</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in templates">
            <td class="i-center-td">{{item.id}}</td>
            <td>
                <a href="<?= link_to('admin_tmp', array('action' => 'edit')) ?>?id={{item.id}}">{{item.name}}</a>
            </td>
            <td>{{item.edited}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="fa fa-list"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_tmp', array('action' => 'edit')) ?>?id={{item.id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
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
    window._templates = <?= $templates? \Delorius\Utils\Json::encode((array)$templates): '[]' ?>;
</script>


