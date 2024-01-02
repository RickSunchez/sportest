<div ng-controller="PageListController" ng-init='init()'>

    <div>
        <a href="<?= link_to('admin_page', array('action' => 'add')); ?>?domain={{select_domain}}"
           class="btn btn-xs btn-info">Добавить
            раздел</a>
    </div>
    <br/>
    <? if ($multi): ?>
        <div class="col-sm-4">
            <lable>Выберите сайт:</lable>
            <select ng-model="select_domain" ng-options="d.name as d.host for d in domain" ng-change="select()">
            </select>
        </div>
    <? endif ?>

    <br clear="all"/>

    <h3>Страницы сайта</h3>

    <div class="pages_list" ng-repeat="page in getPages(0)" ng-include="'tpl_block'"></div>


    <script type="text/ng-template" id="tpl_block">

        <div class="clearfix item" ng-show="show_page(page)" ng-dblclick="show_page_child(page)"
             ng-class="{'selected':is_selected(page),'unselected':!is_selected(page),unsee:page.status==0}">
            <div class="btn-group fl_r">
                <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="glyphicon glyphicon-cog"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li><a href="<?= link_to('admin_page', array('action' => 'redirect')); ?>?id={{page.id}}"
                           target="_blank">
                            <i class="glyphicon glyphicon-share-alt"></i>
                            Перейти на страницу
                        </a></li>
                    <li><a tabindex="-1" href="#" ng-click="add(page.id)">
                            <i class="glyphicon glyphicon-plus"></i>
                            Добавить подраздел</a></li>
                    <li class="divider"></li>
                    <li><a tabindex="-1" href="#" ng-click="edit(page.id)">
                            <i class="glyphicon glyphicon-edit"></i>
                            Редактировать
                        </a></li>
                    <li><a tabindex="-1" href="#" ng-click="delete(page.id)">
                            <i class="glyphicon glyphicon-trash"></i>
                            Удалить
                        </a></li>
                </ul>
            </div>
            <div class="pos fl_r" style="margin: 0 5px 0 0;">
                <i class="glyphicon glyphicon-chevron-up" ng-click="down(page)"></i>
                <input ng-model="page.pos" style="width: 20px; text-align: center;" ng-blur="change_pos(page)"/>
                <i class="glyphicon glyphicon-chevron-down" ng-click="up(page)"></i>
            </div>
            <div class="field" style="color:#ccc;padding: 0 5px;">
                <i class="glyphicon" ng-show="has_child(page)"
                   ng-class="{'glyphicon-folder-open':is_selected(page),'glyphicon-folder-close':!is_selected(page)}"
                   ng-click="show_page_child(page)"></i>
                &nbsp;
                ID:{{page.id}}
            </div>
            <label class="b-input-upload_cat" for="img_{{page.id}}">
                <img ng-src="{{getImageSrc(page.id)}}" alt=""/>
                <input id="img_{{page.id}}" type="file" ng-file-select="onFileSelect($files,page.id)"
                       title="Загрузить фото"/>
            </label>

            <div class="field">
                <a href="javascript:;" ng-click="edit(page.id)">{{page.short_title}}</a>
                <span ng-if="page.main == 0">
                    <i ng-click="main(page.id,1)" class="glyphicon glyphicon-star-empty" style="cursor: pointer;"></i>
                </span>
                <span ng-if="page.main == 1">
                    <i ng-click="main(page.id,0)" class="glyphicon glyphicon-star"
                       style="cursor: pointer;color: #FF0000"></i>
                </span>
                <span ng-if="page.status == 0">
                    <i ng-click="status(page.id,1)" class="glyphicon glyphicon-eye-close" style="cursor: pointer;"></i>
                </span>
                <span ng-if="page.status == 1">
                    <i ng-click="status(page.id,0)" class="glyphicon glyphicon-eye-open" style="cursor: pointer;"></i>
                </span>
            </div>
        </div>
        <div ng-class="{'selected':is_selected(page),'unselected':!is_selected(page)}">
            <div class="child" ng-repeat="page in getPages(page.id)" ng-include="'tpl_block'"></div>
        </div>
    </script>


</div>

<style type="text/css">
    .field i {
        margin: 0 5px;
    }

    .pages_list .child {
        margin-left: 10px;
    }

    .pages_list .item .field {
        float: left;
    }

    .pages_list .item {
        padding: 5px 5px;
        border: 1px solid #ccc;
        margin: 1px 0;
        cursor: pointer;
    }

    .pages_list .unselected .child,
    .pages_list .unselected .item {
        display: none;
    }

    .pages_list .unsee .field a {
        text-decoration: line-through;
    }

    .pages_list .item:hover {
        background: #f5f5f5;
    }

    .pages_list .item:hover i {
        color: #000;
    }

    .pos i {
        cursor: pointer;
    }


</style>
<script type="text/javascript">
    window._pages = <?= $pages ? \Delorius\Utils\Json::encode((array)$pages): '{}'?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode($images): '[]' ?>;
    window._domain = <?= $domain ? \Delorius\Utils\Json::encode((array)$domain): '{}'?>;
</script>




