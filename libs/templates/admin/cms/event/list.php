<div ng-controller="EventListCtrl" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_event', array('action' => 'add')) ?>"
           title="Добавить событие">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br clear="all"/>

    <? if ($multi): ?>
        <div class="b-selects" >
            <label for="domains">Выберите сайт:</label>
            <select id="domains" ui-select2 ng-model="select_domain" ng-change="select()">
                <option value="{{d.name}}" ng-repeat="d in domain">{{d.host}}</option>
            </select>
        </div>
    <? endif ?>

    <div>
        <div>Кол-во событий: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20"><i class="glyphicon glyphicon-star"></i></th>
            <th width="20" class="i-center-td"><i class="glyphicon glyphicon-eye-open"></i></th>
            <th>Название</th>
            <th ng-if="show_cat()">Категория</th>
            <? if ($multi): ?>
                <th>Домен</th>
            <? endif; ?>
            <th width="140">Начало/Конец</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in events">
            <td class="i-center-td">{{item.id}}</td>
            <td class="i-center-td">
                <span ng-if="item.main == 0">
                    <i ng-click="main(item.id,1)" class="glyphicon glyphicon-star-empty" style="cursor: pointer;"></i>
                </span>
                 <span ng-if="item.main == 1">
                    <i ng-click="main(item.id,0)" class="glyphicon glyphicon-star" style="cursor: pointer;color: #FF0000"></i>
                </span>
            </td>
            <td class="i-center-td">
                <span ng-if="item.status == 1">
                    <i ng-click="status(item.id,0)" class="glyphicon glyphicon-eye-open" style="cursor: pointer;color: green;"></i>
                </span>
                <span ng-if="item.status == 0">
                    <i ng-click="status(item.id,1)" class="glyphicon glyphicon-eye-close" style="cursor: pointer;"></i>
                </span>
            </td>
            <td>
                <a href="<?= link_to('admin_event', array('action' => 'edit')) ?>?id={{item.id}}">{{item.name}}</a>
                <div ng-if="item.location">{{item.location}}</div>
            </td>
            <td class="i-center-td" ng-if="show_cat()">{{getNameCat(item.cid)}}</td>
            <? if ($multi): ?>
                <td>{{item.site}}</td>
            <? endif; ?>
            <td>{{item.date_cr}}<br />{{item.date_end}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_event', array('action' => 'edit')) ?>?id={{item.id}}">
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
    window._events = <?= $events? \Delorius\Utils\Json::encode((array)$events): '[]' ?>;
    window._domain = <?= $domain ? \Delorius\Utils\Json::encode((array)$domain): '{}'?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_EVENT,'placeholder'=>'Без категории'));?>;
    window._select_domain = '<?= $get['domain']?$get['domain']:'www'?>';
</script>


