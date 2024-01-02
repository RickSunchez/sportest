<div ng-controller="CategoriesListCMSCtrl" ng-init='init()'>
    <div class="clearfix">
        <a class="btn btn-info btn-xs"
           href="<?= link_to('admin_cms_category', array('action' => 'add')) ?>?type={{select_types}}"
           title="Добавить родительскую категорию">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
        <span ng-bind-html="getBtnUpdateCount()"></span>
    </div>
    <h2>Категории</h2>

    <div class="b-selects">
        <label style="padding-right: 20px;">Выберите тип:</label>
        <select ui-select2 ng-model="select_types" ng-change="select(select_types)" style="width: 200px;">
            <option value="{{type.id}}" ng-repeat="type in types">{{type.name}}</option>
        </select>
    </div>


    <br clear="all"/>

    <div class="categories_list" ng-repeat="category in getCategories(0)" ng-include="'tpl_block'"></div>


    <div style="margin-top:30px;" ng-if="getCategories(0).length !=0">
        <form class="form-inline" role="form">
            <div class="form-group">
                <select class="form-control select_action">
                    <option value="">Выберите действие</option>
                    <option value="delete">Удалить</option>
                    <option value="active">Активировать</option>
                    <option value="deactivate">Деактивировать</option>
                    <option value="edit">Редактировать</option>
                </select>
            </div>
            <button ng-click="editSelectCats()" type="button" class="btn btn-default">Готово</button>
        </form>
    </div>


    <script type="text/ng-template" id="tpl_block">

        <div class="clearfix item "
             ng-class="{category_child:has_child(category), category_goods:category.object, 'selected':is_selected(category),'unselected':!is_selected(category) }"
             ng-show="show_category(category)" ng-dblclick="show_category_child(category)">
            <div class="btn-group fl_r">
                <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                    <i class="glyphicon glyphicon-cog"></i>
                </a>
                <ul class="dropdown-menu pull-right">
                    <li ng-bind-html="getLinkAddObject(category)"></li>
                    <li>
                        <a href="<?= link_to('admin_cms_category', array('action' => 'add')) ?>?id={{category.cid}}"><i
                                    class="glyphicon glyphicon-plus"></i> Добавить подкатегорию</a>
                    </li>
                    <li role="presentation" class="divider"></li>
                    <li><a href="<?= link_to('admin_cms_category', array('action' => 'edit')) ?>?id={{category.cid}}">
                            <i class="glyphicon glyphicon-pencil"></i> Редактировать
                        </a></li>
                    <li><a tabindex="-1" href="#" ng-click="delete(category.cid)"><i
                                    class="glyphicon glyphicon-trash"></i> Удалить</a></li>
                </ul>
            </div>
            <div class="fl_r" style="margin: 0 5px 0 0; width: 60px;">
                <i class="glyphicon glyphicon-chevron-up" ng-click="down(category)"></i>
                <input ng-model="category.pos" style="width: 20px; text-align: center;height: 20px;"
                       ng-blur="change_pos(category)"/>
                <i class="glyphicon glyphicon-chevron-down" ng-click="up(category)"></i>
            </div>
            <div class=" fl_r" style="padding: 0 20px 0 0;">
                <span ng-if="category.object" ng-bind-html="getLinkObject(category)"></span>
                <span ng-if="category.children">[Подкатегорий: {{category.children}}]</span>
            </div>
            <div class="fl_l">
                <input class="catsIds" type="checkbox" value="{{category.cid}}" name="ids[]"/>
            </div>
            <div class="field" style="color:#ccc;padding: 0 5px;">
                <i class="glyphicon" ng-show="has_child(category)"
                   ng-class="{'glyphicon-folder-open':is_selected(category),'glyphicon-folder-close':!is_selected(category)}"
                   ng-click="show_category_child(category)"></i>
                &nbsp;
                ID:{{category.cid}}
            </div>
            <div class="b-input-upload_cat">
                <img width="20" ng-src="{{getImageSrc(category.cid)}}" alt=""/>
                <input type="file" ng-file-select="onFileSelect($files,category.cid)"/>
            </div>
            <div class="field">
                <a href="<?= link_to('admin_cms_category', array('action' => 'edit')) ?>?id={{category.cid}}">
                    <span ng-show="category.status == 1">{{category.name}}</span>
                    <span style="color: #ccc;" ng-show="category.status == 0">{{category.name}}</span>
                </a>
            </div>
        </div>

        <div ng-class="{'selected':is_selected(category),'unselected':!is_selected(category)}">
            <div class="child" ng-repeat="category in getCategories(category.cid)" ng-include="'tpl_block'"></div>
        </div>

    </script>

</div>


<style type="text/css">
    .categories_list .child {
        margin-left: 10px;
    }

    .categories_list .item .field {
        float: left;
    }

    .categories_list .item {
        padding: 5px 5px;
        border: 1px solid #ccc;
        margin: 1px 0;
        cursor: pointer;
    }

    .categories_list .unselected .child,
    .categories_list .unselected .item {
        display: none;
    }

    .categories_list .item:hover {
        background: #f5f5f5;
    }

    .categories_list .item:hover i {
        color: #000;
    }


</style>

<script type="application/javascript">
    window._categories = <?= $categories ? \Delorius\Utils\Json::encode($categories) : '[]' ?>;
    window._types = <?= $types ? \Delorius\Utils\Json::encode($types) : '[]' ?>;
    window._config_type = <?= $config['category_type'] ? \Delorius\Utils\Json::encode($config['category_type']) : '[]' ?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode($images) : '[]' ?>;
    window._type_id = <?= $type_id ? $type_id : \CMS\Catalog\Entity\Category::TYPE_NEWS ?>;
</script>


