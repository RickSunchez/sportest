<div ng-controller="SliderListCtrl" ng-init="init()">

    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_slider', array('action' => 'add')) ?>"
           title="Добавить слайдер">
            <i class="glyphicon glyphicon-plus"></i> Добавить слайдер
        </a>
    </div>
    <br clear="all"/>

    <div ng-if="select_code" style="padding-bottom: 20px;">
        Выбран слайдер: {{select_code}}
        <i style="cursor: pointer" class="glyphicon glyphicon-remove"
                                         ng-click="select()"></i>
    </div>

    <div>
        <div>Кол-во слайдеов: <?= $pagination->getItemCount() ?></div>
    </div>
    <table class="table table-condensed table-bordered table-hover">

        <tr>
            <th width="20" style="text-align: center;"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th  class="i-center-td"  width="60">Фото</th>
            <th>Название</th>
            <th  class="i-center-td" >Код</th>
            <th width="50" >Приоритет</th>
            <th width="20"></th>
        </tr>

        <tr ng-repeat="item in sliders">
            <td class="i-center-td">
                <span ng-if="item.status == 1">
                    <i ng-click="status(item.id,0)"
                       class="glyphicon glyphicon-eye-open"
                       style="cursor: pointer;color: green;"></i>
                </span>
                <span ng-if="item.status == 0">
                    <i ng-click="status(item.id,1)"
                       class="glyphicon glyphicon-eye-close"
                       style="cursor: pointer;"></i>
                </span>
            </td>
            <td><img width="50" ng-src="{{getImageSrc(item.id)}}" alt=""/></td>
            <td>
                <a title="Редактировать" href="<?= link_to('admin_slider', array('action' => 'edit')) ?>?id={{item.id}}">{{item.title}}</a>
            </td>
            <td class="i-center-td"><a class="name" href="javascript:;" ng-click="select(item.code)">{{item.code}}</a></td>
            <td class="i-center-td" >{{item.pos}}</td>
            <td  class="i-center-td"  >
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_slider', array('action' => 'edit')) ?>?id={{item.id}}">
                                <i class="glyphicon glyphicon-edit"></i> Редактировать
                            </a>
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

    <?= $pagination; ?>
</div>

<script type="text/javascript">
    window._images= <?= $images ? \Delorius\Utils\Json::encode($images) : '[]' ;?>;
    window._sliders = <?= $sliders? \Delorius\Utils\Json::encode((array)$sliders): '[]' ?>;
</script>