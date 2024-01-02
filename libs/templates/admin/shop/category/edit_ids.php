<div ng-controller="CategoryIdsController" ng-init="init()">
    <div class="clearfix btn-group ">
        <a title="Назад" href="<?= link_to('admin_category', array('action' => 'list', 'type_id' => $type_id)); ?>"
           class="btn btn-danger btn-xs"><i class=" glyphicon glyphicon-arrow-left"></i></a>
    </div>
    <br/>

    <h1>Категории</h1>

    <div>ID: {{ids|json}}</div>
    <br/>
    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO параметры</a></li>
        <li><a href="#filter" data-toggle="tab">Фильтры</a></li>
        <li><a href="#import" data-toggle="tab">Синхронизация</a></li>
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

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Вложенность <img style="width: 20px" src="/source/images/load.svg" alt="" class="category_load "></label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="category.pid" style="width: 100%">
                            <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                    ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                            </option>
                        </select>
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
                    <label class="col-sm-2 control-label" for="text_below">Текст  снизу</label>

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
                    <label for="inputall" class="col-sm-2 control-label">Весь список</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="category.show_all" ng-true-value="'1'" id="inputall"
                                   ng-false-value="'0'"/> Отобразить весь список</p>
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
    window._type_id = <?= $type_id?>;
    window._ids = '<?= $ids?>';
    window._filter_types = <?= $filter_types ? \Delorius\Utils\Json::encode((array)$filter_types): '[]'?>;
    window._filter_goods_params = <?= $filter_goods_params ? \Delorius\Utils\Json::encode((array)$filter_goods_params): '[]'?>;
    window._chara = <?= $chara ? \Delorius\Utils\Json::encode((array)$chara): '[]'?>;
</script>




