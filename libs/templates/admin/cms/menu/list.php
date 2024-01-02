<div ng-controller="MenuListController" ng-init='init()'>

    <div>
        <a href="javascript:;" ng-click="addForm()" class="btn btn-xs btn-info ">Добавить пункт меню</a>
    </div>

    <br clear="all"/>

    <h3>Меню</h3>

    <div ng-if="select_code" style="padding-bottom: 20px;">
        Выбрано меню: {{select_code}}
        <i style="cursor: pointer" class="glyphicon glyphicon-remove" ng-click="select()"></i>
    </div>

    <div class="menus_list" ng-repeat="menu in getMenus(0)" ng-include="'tpl_block'"></div>


    <script type="text/ng-template" id="tpl_block">

        <div class="clearfix item" ng-show="show_menu(menu)" ng-dblclick="show_menu_child(menu)"
             ng-class="{'selected':is_selected(menu),'unselected':!is_selected(menu),unsee:menu.status==0}">

            <div class="b-table">
                <div class="b-table-cell _folder" ng-show="has_child(menu)">
                    <i class="glyphicon"
                       ng-class="{'glyphicon-folder-open':is_selected(menu),'glyphicon-folder-close':!is_selected(menu)}"
                       ng-click="show_menu_child(menu)"></i>
                </div>
                <div class="b-table-cell _id">ID:{{menu.id}}</div>
                <div class="b-table-cell _image">
                    <label class="b-input-upload_cat" for="img_{{menu.id}}">
                        <img ng-src="{{getImageSrc(menu.id)}}" alt=""/>
                        <input id="img_{{menu.id}}" type="file" ng-file-select="onFileSelect($files,menu.id)"
                               title="Загузить фото"/>
                    </label>
                </div>
                <div class="b-table-cell _show">
                    <span ng-if="menu.status == 0">
                        <i ng-click="status(menu.id,1)" class="glyphicon glyphicon-eye-close"
                           style="cursor: pointer;"></i>
                    </span>
                    <span ng-if="menu.status == 1">
                        <i ng-click="status(menu.id,0)" class="glyphicon glyphicon-eye-open"
                           style="cursor: pointer;"></i>
                    </span>
                </div>
                <div class="b-table-cell _name">
                    <a class="name" href="javascript:;" ng-click="edit(menu.id)">{{menu.name}}</a>
                    ({{menu.type_name}} = <b>{{menu.value}}</b>)
                </div>
                <div class="b-table-cell _code">
                    <a class="name" href="javascript:;" ng-click="select(menu.code)">{{menu.code}}</a>
                </div>
                <div class="b-table-cell _pos i-center-td">
                    <input ng-model="menu.pos" style="width: 40px; text-align: center;" ng-blur="change_pos(menu)"/>
                </div>
                <div class="b-table-cell _menu">
                    <div class="btn-group">
                        <a class="btn btn-xs btn-info dropdown-toggle" data-toggle="dropdown" href="#">
                            <i class="glyphicon glyphicon-cog"></i>
                        </a>
                        <ul class="dropdown-menu pull-right">
                            <li><a tabindex="-1" href="#" ng-click="add(menu.id)">
                                    <i class="glyphicon glyphicon-plus"></i>
                                    Добавить подраздел</a></li>
                            <li class="divider"></li>
                            <li><a tabindex="-1" href="#" ng-click="edit(menu.id)">
                                    <i class="glyphicon glyphicon-edit"></i>
                                    Редактировать
                                </a></li>
                            <li><a tabindex="-1" href="#" ng-click="delete(menu.id)">
                                    <i class="glyphicon glyphicon-trash"></i>
                                    Удалить
                                </a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div ng-class="{'selected':is_selected(menu),'unselected':!is_selected(menu)}">
            <div class="child" ng-repeat="menu in getMenus(menu.id)" ng-include="'tpl_block'"></div>
        </div>
    </script>


    <div id="form" class="b-popup _form_menu mfp-hide ">
        <div class="title">Редактирования пункта меню</div>

        <div class="well form-horizontal">


            <div ng-show="form.id">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="image">Загрузка</label>

                    <div class="col-sm-10">
                        <input type="file" ng-file-select="onFileSelect($files,form.id)">
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="image">Фото</label>

                    <div class="col-sm-10">
                        <img ng-src="{{getImageSrc(form.id)}}" alt="" width="100"/>
                        <a ng-click="deleteImage(form.id)" href="javascript:;">Удалить</a>
                    </div>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="name">Название</label>

                <div class="col-sm-10">
                    <input type="text" id="name" ng-model="form.name" class="form-control"
                           placeholder="Название пункта меню"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="value">Значение</label>

                <div class="col-sm-10">

                    <div class="b-table">
                        <div class="b-table-cell _form_select_type" style="width:150px;">
                            <p class="form-control-static">
                                <select ui-select2 name=type ng-model="form.type" style="width: 100%;">
                                    <option value="{{type.id}}" ng-repeat="type in types">{{type.name}}</option>
                                </select>
                            </p>
                        </div>
                        <div class="b-table-cell">
                            <input type="text" id="value" ng-model="form.value" class="form-control"
                                   placeholder="Значение ссылки"/>
                        </div>
                    </div>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="code">Код</label>

                <div class="col-sm-10">
                    <input type="text" id="code" ng-model="form.code" placeholder="Код меню" class="form-control"/>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="style">Css</label>

                <div class="col-sm-10">
                    <input type="text" id="style" ng-model="form.style" placeholder="Классы стилей"
                           class="form-control"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="pid">Родитель</label>

                <div class="col-sm-10">
                    <input type="text" id="pid" ng-model="form.pid" placeholder="ID" style="width: 60px;"
                           class="form-control"/>
                    <span class="help-block">ID родителя</span>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="pos">Позиция</label>

                <div class="col-sm-10">
                    <input type="text" id="pos" ng-model="form.pos" style="width: 60px;" class="form-control"/>
                    <span class="help-block">Не обязательное поле</span>
                </div>
            </div>




            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" ng-click="save()" class="btn btn-info">Сохронить</button>
                </div>
            </div>

        </div>

    </div>

</div>

<style type="text/css">

    .b-table-cell {
        vertical-align: middle !important;
    }

    ._id {
        width: 50px;
        text-align: center;
    }

    ._folder {
        width: 20px;
    }

    ._show {
        width: 25px;
        text-align: center;
    }

    ._image {
        width: 40px;
    }

    ._pos {
        width: 80px;
    }

    ._menu {
        width: 30px;
    }

    .menus_list .child {
        margin-left: 10px;
    }

    .menus_list .item {
        padding: 5px 5px;
        border: 1px solid #ccc;
        margin: 1px 0;
        cursor: pointer;
    }

    .menus_list .unselected .child,
    .menus_list .unselected .item {
        display: none;
    }

    .menus_list .unsee .name {
        text-decoration: line-through;
    }

    .menus_list .item:hover {
        background: #f5f5f5;
    }

    .menus_list .item:hover i {
        color: #000;
    }

    .pos i {
        cursor: pointer;
    }

    ._form_menu {
        width: 800px;
    }

    ._form_select_type {
        width: 105px;
        padding-right: 5px;
    }

    ._code {
        width: 100px;
    }


</style>
<script type="text/javascript">
    window._menus = <?= $menus ? \Delorius\Utils\Json::encode((array)$menus): '{}'?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode($images): '[]' ?>;
    window._types = <?= $types ? \Delorius\Utils\Json::encode($types): '[]' ?>;
</script>