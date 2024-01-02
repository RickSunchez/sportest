<div ng-controller="CategoryController" ng-init="init()">
    <div class="clearfix btn-group ">
        <a title="Назад" href="<?= link_to('admin_category', array('action' => 'list')); ?>"
           class="btn btn-danger btn-xs"><i class=" glyphicon glyphicon-arrow-left"></i></a>
        <a class="btn btn-info btn-xs" href="<?= link_to('admin_category', array('action' => 'add')) ?>{{query}}"
           title="Добавить категорию">
            <i class="glyphicon glyphicon-plus"></i>
        </a>

    </div>
    <br/>

    <h1>Категория</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li><a href="#filter" data-toggle="tab">Фильтры</a></li>
        <li><a ng-show="category.cid" href="#popular" data-toggle="tab">Популярные товары</a></li>
        <li><a href="#import" data-toggle="tab">Синхронизация</a></li>
        <li><a ng-show="category.cid" href="#options" data-toggle="tab">Операции</a></li>
        <li class="dropdown">
            <a href="#dop" class="dropdown-toggle" data-toggle="dropdown" href="#">SEO <b
                        class="caret"></b></a>
            <ul aria-labelledby="dop" role="menu" class="dropdown-menu">
                <li><a href="#meta" data-toggle="tab">Meta Date</a></li>
                <li><a href="#meta_goods" data-toggle="tab">SEO товара</a></li>
                <li ng-show="category.cid">
                    <a href="<?= link_to('admin_category_collection', array('action' => 'list', 'cid' => $category['cid'])) ?>">Подборки</a>
                </li>
                <li ng-show="category.cid">
                    <a href="<?= link_to('admin_category_filter', array('action' => 'list', 'cid' => $category['cid'])) ?>">Фильры</a>
                </li>

            </ul>
        </li>

    </ul>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="category.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать категорию в магазине</p>
                    </div>
                </div>

                <div ng-show="category.cid">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="image">Загрузка</label>

                        <div class="col-sm-10">
                            <input type="file" ng-file-select="onFileSelect($files,category.cid)">
                        </div>
                    </div>

                    <div class="form-group" ng-if="image.image_id">
                        <label class="col-sm-2 control-label" for="image">Фото</label>

                        <div class="col-sm-10">
                            <img ng-src="{{image.preview}}" alt="" width="100"/>
                            <a href="<?= link_to('admin_image', array('action' => 'edit')) ?>?id={{image.image_id}}"
                               title="Редактировать" class="btn btn-info btn-xs">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Вложенность <img style="width: 20px"
                                                                                      src="/source/images/load.svg"
                                                                                      alt=""
                                                                                      class="category_load "></label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="category.pid" style="width: 100%">
                            <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                    ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="category.name" class="form-control"
                               placeholder="Название категории"/>

                        <p class="help-block">Для меню, списков и т.п.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="header">Полное название категории</label>

                    <div class="col-sm-10">
                        <input type="text" id="header" ng-model="category.header" class="form-control"
                               placeholder="Заголовок категории"/>

                        <p class="help-block">Может использоваться в h1 для текущей категории</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="category.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text_top">Текст сверху</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text_top" ng-model="category.text_top"
                                  class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text_below">Текст снизу</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text_below" ng-model="category.text_below"
                                  class="form-control"></textarea>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="category.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Не обязательное поле</span>
                    </div>
                </div>


                <div class="form-group">
                    <label for="show_cats" class="col-sm-2 control-label">Под категории</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="category.show_cats" ng-true-value="'1'" id="show_cats"
                                   ng-false-value="'0'"/> Отобразить только категории</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс шаблона категории</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="category.prefix" class="form-control"
                               placeholder="shop/category/list_*"/>

                        <p class="help-block">Для выбора не стандартного отображения категории
                            shop/category/list_{prefix}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inputall" class="col-sm-2 control-label">Весь список</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="category.show_all" ng-true-value="'1'" id="inputall"
                                   ng-false-value="'0'"/> Отобразить весь список продукции</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс шаблона списка товаров</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="category.prefix_goods" class="form-control"
                               placeholder="shop/goods/list_*"/>

                        <p class="help-block">Для выбора не стандартного отображения списка товаров
                            shop/goods/list_{prefix}</p>
                    </div>
                </div>

            </div>
            <!-- #desc -->

            <div class="tab-pane" id="meta">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">Заголовок страницы</label>

                    <div class="col-sm-10">
                        <input type="text" id="title" ng-model="meta.title" class="form-control"
                               placeholder="Заголовок страницы"/>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="keys">Ключевые слова</label>

                    <div class="col-sm-10">
                        <textarea id="keys" ng-model="meta.keys" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="desc">Краткое описания страницы</label>

                    <div class="col-sm-10">
                        <textarea id="desc" ng-model="meta.desc" class="form-control" onblur=""></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="redirect">Редирект</label>

                    <div class="col-sm-10">
                        <input type="text" id="redirect" ng-model="meta.redirect" class="form-control"
                               placeholder="Адрес ссылки"/>
                        <span class="help-block">Если необходиво перенаправить пользователя при переходе</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_title">og:title</label>

                    <div class="col-sm-10">
                        <input id="og_title" ng-model="meta.options.og.title" class="form-control"/>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_description">og:description</label>

                    <div class="col-sm-10">
                        <textarea id="og_description" ng-model="meta.options.og.description" class="form-control"
                                  onblur=""></textarea>
                    </div>
                </div>
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="og_image">og:image</label>

                    <div class="col-sm-10">
                        <input id="og_image" ng-model="meta.options.og.image" class="form-control"/>
                    </div>
                </div>

            </div>
            <!-- #meta -->

            <div class="tab-pane" id="meta_goods">
                <div class="form-group">
                    <label class="col-sm-2 control-label" for="title">Заголовок страницы</label>

                    <div class="col-sm-10">
                        <input type="text" id="title" ng-model="meta_goods.title" class="form-control"
                               placeholder="Заголовок страницы"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="desc">Краткое описания страницы</label>

                    <div class="col-sm-10">
                        <textarea id="desc" ng-model="meta_goods.desc" class="form-control" onblur=""></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text_goods">Описания товара</label>

                    <div class="col-sm-10">
                        <textarea id="text_goods" ng-model="meta_goods.text" class="form-control"></textarea>
                    </div>
                </div>

            </div>
            <!-- #meta_goods -->

            <div class="tab-pane" id="filter">

                <div ng-show="item.show" id="inc_{{item.inc}}" class="form-group" ng-repeat="item in filters">
                    <div class="col-sm-3">
                        <input type="text" id="name" ng-model="item.name" class="form-control"
                               placeholder="Названия фильтра"/>
                    </div>
                    <div class="col-sm-3">
                        <select id="input_filter_{{item.inc}}" ng-model="item.type_id"
                                class="form-control select_update"
                                data-value="{{item.type_id}}" ng-change="selectFilter(item.inc)">
                            <option value="0">--Выберите--</option>
                            <option ng-repeat="f in filter_types" value="{{f.id}}">
                                {{f.name}}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-3">

                        <select id="input_value_{{item.inc}}" ng-model="item.value" class="form-control select_update"
                                data-value="{{item.value}}" ng-change="selectFilterValue(item)">
                            <option value="0">--Выберите--</option>
                            <option ng-repeat="v in getValue(item.inc)" value="{{v.id}}">
                                {{v.name}}
                            </option>
                        </select>

                    </div>
                    <div class="col-sm-1">
                        <input type="text" id="pos" ng-model="item.pos" class="form-control" placeholder="0"
                               parser-int/>
                    </div>

                    <div class="col-sm-1">
                        <a ng-click="deleteFilter(item.inc)" class="btn btn-danger" href="javascript:void(0)">
                            <i class="glyphicon glyphicon-trash"></i></a>
                    </div>

                </div>


                <div class="form-group">
                    <div class=" col-sm-10">
                        <a href="javascript:void(0);" ng-click="addFilter()" class="btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-plus"></i>
                            Добавить фильтр
                        </a>
                    </div>
                </div>

            </div>
            <!-- #filter -->


            <div class="tab-pane" id="popular">

                <div style="padding-bottom: 20px">
                    <span style="font-size: 16px;margin-right: 10px;">Товары
                        <img style="width: 20px"
                             src="/source/images/load.svg"
                             alt=""
                             class="product_load"></span>
                    <a style="margin-right: -10px" title="Добвить товар" class="btn btn-success btn-xs popup-link-ajax"
                       href="<?= link_to('admin_goods_data', array('action' => 'goodsList')) ?>?cid={{category.cid}}&type_id={{category.type_id}}">
                        <i class="glyphicon glyphicon-plus"></i>
                    </a>
                </div>

                <table class="table table-condensed table-bordered table-hover table-middle">
                    <tr>
                        <th class="i-center-td" width="20">ID</th>
                        <th width="75">Арт.</th>
                        <th>Название</th>
                        <th class="i-center-td" width="50">
                            <i title="Приоритет" class="glyphicon glyphicon-sort-by-attributes-alt"></i>
                        </th>
                        <th width="20"></th>
                    </tr>
                    <tr ng-repeat="item in items">
                        <td>{{item.product_id}}</td>
                        <td>{{getProduct(item.product_id).article}}</td>
                        <td>{{getProduct(item.product_id).name}}</td>
                        <td class="i-center-td">
                            <input ng-model="item.pos" style="width: 100%; text-align: center;"
                                   ng-blur="saveProduct(item)"
                                   class="pos"/>
                        </td>
                        <td class="i-center-td">
                            <a title="Удалить" href="javascript:;" ng-click="deleteProduct(item.id)">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </td>
                    </tr>
                </table>


            </div>
            <!-- #popular -->

            <div class="tab-pane" id="import">
                <fieldset>
                    <legend>Импорт</legend>
                    <div class="form-group">
                        <label for="external_change" class="col-sm-2 control-label">Изменение</label>

                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="checkbox" ng-model="category.external_change" ng-true-value="'1'"
                                       id="external_change"
                                       ng-false-value="'0'"/> Учитывать при импорте</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="external_id">Внешний ИД</label>

                        <div class="col-sm-10">
                            <input type="text" id="external_id" ng-model="category.external_id" class="form-control"/>
                        </div>
                    </div>
                </fieldset>
            </div>
            <!-- #import -->


            <div class="tab-pane" id="options">
                <fieldset>
                    <legend>Операции</legend>

                    <table class="table table-condensed table-bordered table-hover" style="background: #fff;">
                        <tr>
                            <td class="i-middle-td">Отключить все товары этой категории</td>
                            <td width="100" class="i-center-td">
                                <a class="btn btn-danger" href="javascript:;" ng-click="operation_off_goods()">Отключить</a>
                            </td>
                        </tr>

                        <tr>
                            <td class="i-middle-td">Отключить все категории и товары этой категории</td>
                            <td width="100" class="i-center-td">
                                <a class="btn btn-danger" href="javascript:;" ng-click="operation_off_cats_goods()">Отключить</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="i-middle-td">Удалить все товары этой категории</td>
                            <td width="100" class="i-center-td">
                                <a class="btn btn-danger" href="javascript:;" ng-click="operation_del_goods()">Удалить</a>
                            </td>
                        </tr>
                        <tr>
                            <td class="i-middle-td">Удалить все категории и товары этой категории</td>
                            <td width="100" class="i-center-td">
                                <a class="btn btn-danger" href="javascript:;" ng-click="operation_del_cats_goods()">Удалить</a>
                            </td>
                        </tr>
                    </table>


                </fieldset>
            </div>
            <!-- #import -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._category = <?= $category ? \Delorius\Utils\Json::encode((array)$category) : '{status:"1",show_all:"0",external_change:"1"}'?>;
    window._meta = <?= $meta ? \Delorius\Utils\Json::encode((array)$meta) : '{}'?>;
    window._meta_goods = <?= $meta_goods ? \Delorius\Utils\Json::encode((array)$meta_goods) : '{}'?>;
    window._pid = <?= ($pid) ? (int)$pid : '0';?>;
    window._type_id = <?= ($type_id) ? (int)$type_id : \Shop\Catalog\Entity\Category::TYPE_GOODS;?>;
    window._image = <?= $image ? \Delorius\Utils\Json::encode((array)$image) : 'null'?>;

    window._filters = <?= $filters ? \Delorius\Utils\Json::encode((array)$filters) : '[]'?>;
    window._filter_types = <?= $filter_types ? \Delorius\Utils\Json::encode((array)$filter_types) : '[]'?>;
    window._filter_goods_params = <?= $filter_goods_params ? \Delorius\Utils\Json::encode((array)$filter_goods_params) : '[]'?>;
    window._chara = <?= $chara ? \Delorius\Utils\Json::encode((array)$chara) : '[]'?>;
</script>




