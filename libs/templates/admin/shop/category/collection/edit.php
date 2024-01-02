<div ng-controller="CategoryCollectionController" ng-init="init(<?= $cid ?>)">

    <div class="clearfix btn-group ">
        <a title="Назад" href="<?= link_to('admin_category_collection', array('action' => 'list', 'cid' => $cid)); ?>"
           class="btn btn-danger btn-xs"><i class=" glyphicon glyphicon-arrow-left"></i></a>
        <a class="btn btn-info btn-xs"
           href="<?= link_to('admin_category_collection', array('action' => 'add', 'cid' => $cid)) ?>"
           title="Добавить подборку">
            <i class="glyphicon glyphicon-plus"></i>
        </a>

    </div>
    <br/>

    <h1>Категория</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO параметры</a></li>
        <li><a href="#filter" data-toggle="tab">Фильтры</a></li>
        <li><a href="#params" data-toggle="tab">Параметры</a></li>
    </ul>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="collection.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать категорию в магазине</p>
                    </div>
                </div>

                <div class="form-group">
                    <label for="type" class="col-sm-2 control-label">Вид</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="collection.type" ng-true-value="'1'" id="type"
                                   ng-false-value="'0'"/> Основной тип подборки</p>
                    </div>
                </div>

                <div ng-show="collection.id">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="image">Загрузка</label>

                        <div class="col-sm-10">
                            <input type="file" ng-file-select="onFileSelect($files,collection.id)">
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
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="collection.name" class="form-control"
                               placeholder="Название подборки"/>

                        <p class="help-block">Для меню, списков и т.п.</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="header">Заголовок подборки</label>

                    <div class="col-sm-10">
                        <input type="text" id="header" ng-model="collection.header" class="form-control"
                               placeholder="Заголовок подборки"/>

                        <p class="help-block">Может использоваться в h1 для текущей подборки</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="collection.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text_top">Текст сверху</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text_top" ng-model="collection.text_top"
                                  class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text_below">Текст  снизу</label>

                    <div class="col-sm-10">
                        <textarea name="text" id="text_below" ng-model="collection.text_below"
                                  class="form-control"></textarea>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="collection.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Не обязательное поле</span>
                    </div>
                </div>


                <div class="form-group">
                    <label for="inputall" class="col-sm-2 control-label">Весь список</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="collection.show_all" ng-true-value="'1'" id="inputall"
                                   ng-false-value="'0'"/> Отобразить весь список продукции</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс шаблона списка товаров</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="collection.prefix" class="form-control"
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

            <div class="tab-pane" id="filter">

                <h2 class="tab-pane-title">Фильтры для категории</h2>

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

            <div class="tab-pane" id="params">

                <h2 class="tab-pane-title">Параметры фильтрации товара</h2>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="price_min">Диапазон цен</label>

                    <div class="col-sm-3">
                        <input type="text" id="price_min" ng-model="collection.price_min" class="form-control"
                               placeholder="c"/>


                    </div>
                    <div class="col-sm-3">
                        <input type="text" id="price_max" ng-model="collection.price_max" class="form-control"
                               placeholder="до"/>


                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="vendors">Производители</label>

                    <div class="col-sm-10">
                        <input type="text" id="vendors" ng-model="collection.vendors" class="form-control"
                               placeholder="IDs производителей"/>

                        <p class="help-block">Список IDs производителй через запятую "," </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="goods">Товары</label>

                    <div class="col-sm-10">
                        <input type="text" id="goods" ng-model="collection.goods" class="form-control"
                               placeholder="IDs товара"/>

                        <p class="help-block">Список IDs товаров через запятую ",", которые обязательно должны быть </p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="goods">Подкатегории</label>

                    <div class="col-sm-10">
                        <input type="text" id="cats" ng-model="collection.cats" class="form-control"
                               placeholder="IDs подкатегорий"/>

                        <p class="help-block">Список IDs подкатегорий через запятую ",", из которых надо выводить
                            товар</p>
                    </div>
                </div>

                <fieldset>
                    <legend>Фильты</legend>

                    <div ng-hide="item.delete" id="inc_{{item.inc}}" class="form-group" ng-repeat="item in chara_goods">
                        <div class="col-sm-4">
                            <select ui-select2 ng-model="item.character_id"
                                    class="form-control chara_goods"
                                    ng-change="selectChara(item.inc)">
                                <option value="0">--Выберите--</option>
                                <option ng-repeat="c in chara" value="{{c.character_id}}">
                                    {{c.group}} {{c.name}}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-4">
                            <select ui-select2 ng-model="item.value_id"
                                    class="form-control chara_goods">
                                <option value="">--Выберите--</option>
                                <option ng-repeat="c_v in chara_values[item.character_id]"
                                        value="{{c_v.value_id}}">
                                    {{c_v.name}} {{c_v.unit}}
                                </option>
                            </select>
                        </div>
                        <div class="col-sm-1">
                            <input type="text" id="pos" ng-model="item.pos" class="form-control" placeholder="0"
                                   parser-int/>
                        </div>

                        <div class="col-sm-1">
                            <a ng-click="deleteChara(item.inc)" class="btn btn-danger" href="javascript:;"><i
                                    class="glyphicon glyphicon-trash"></i></a>
                        </div>

                    </div>


                    <div class="form-group">
                        <div class=" col-sm-10">
                            <a href="javascript:;" ng-click="addChara()" class="btn btn-success btn-xs">
                                <i class="glyphicon glyphicon-plus"></i>
                                Добавить характиеристику
                            </a>
                        </div>
                    </div>

                </fieldset>

            </div>
            <!-- #filter -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Готово</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">

    window._collection = <?= $collection ? \Delorius\Utils\Json::encode((array)$collection): '{status:"1",show_all:"0",cid:"'.$cid.'"}'?>;
    window._meta = <?= $meta? \Delorius\Utils\Json::encode((array)$meta): '{}'?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;


    window._filters = <?= $filters ? \Delorius\Utils\Json::encode((array)$filters): '[]'?>;
    window._filter_types = <?= $filter_types ? \Delorius\Utils\Json::encode((array)$filter_types): '[]'?>;
    window._filter_goods_params = <?= $filter_goods_params ? \Delorius\Utils\Json::encode((array)$filter_goods_params): '[]'?>;
    window._chara = <?= $chara ? \Delorius\Utils\Json::encode((array)$chara): '[]'?>;
    window._chara_goods = <?= $chara_goods? \Delorius\Utils\Json::encode((array)$chara_goods): '[]'?>;
    window._chara_values = <?= $chara_values? \Delorius\Utils\Json::encode((array)$chara_values): '[]'?>;

</script>




