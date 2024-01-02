<div ng-controller="VideoListCtrl" ng-init='init()'>


    <div class="row">
        <div class="col-xs-12">
            <div class="input-group">
                <input ng-model="form.url" type="text" class="form-control" placeholder="http:// ">
                <span class="input-group-btn">
                    <button ng-click="add()" class="btn btn-default" type="button">Добавить видео</button>
                </span>
            </div>
        </div>
    </div>
    <br clear="all"/>
    <div>
        <div>Кол-во видео: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover">
        <tr>
            <th width="20">ID</th>
            <th width="20"><i class="glyphicon glyphicon-star"></i></th>
            <th>Название</th>
            <th ng-if="show_cat()">Категория</th>
            <th width="200">Дата</th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in videos">
            <td class="i-center-td">{{item.id}}</td>
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
            <td><a href="<?= link_to('admin_video', array('action' => 'edit')) ?>?id={{item.id}}">{{item.name}}</a></td>
            <td class="i-center-td" ng-if="show_cat()">{{getNameCat(item.cid)}}</td>
            <td>{{item.created}}</td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_video', array('action' => 'edit')) ?>?id={{item.id}}">
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
    window._videos = <?= $videos? \Delorius\Utils\Json::encode((array)$videos): '[]' ?>;
    window._categories = <?= $this->action('CMS:Admin:Category:catsJson',array('pid'=>0,'typeId'=>\CMS\Catalog\Entity\Category::TYPE_VIDEO,'placeholder'=>'Без категории'));?>;
</script>


