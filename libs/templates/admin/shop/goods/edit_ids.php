<div ng-controller="GoodsEditIdsController" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'list', 'type_id' => $type_id)) ?>">
            <i class=" glyphicon glyphicon-arrow-left"></i>
        </a>
        <a title="Добвить товар" class="btn btn-success btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'add', 'cid' => $cid)) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>

    <h1>Редактирования продукции</h1>

    <div>ID: {{ids|json}}</div>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#goods" data-toggle="tab">Параметры</a></li>
        <li><a href="#chara" data-toggle="tab">Характеристики</a></li>
        <li><a href="#attr" data-toggle="tab">Атрибуты</a></li>
        <li><a href="#desc" data-toggle="tab">Описание</a></li>
        <li><a href="#meta" data-toggle="tab">SEO параметры</a></li>
    </ul>


    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="goods">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="goods.status" ng-true-value="1" id="inputstatus"
                                   ng-false-value="0"/> Показать товар в магазине</p>
                    </div>
                </div>

                <div class="form-group" ng-if="show_select_type()">
                    <label class="col-sm-2 control-label" for="name">Тип</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="goods.ctype" ng-change="select_type(goods.ctype)"
                                style="width: 100%">
                            <option value="{{type.id}}" ng-repeat="type in goods_types">{{type.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Категория <img style="width: 20px" src="/source/images/load.svg" alt="" class="category_load "></label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="goods.cid" style="width: 100%">
                            <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                    ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="goods.name" class="form-control"
                               placeholder="Название товара"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="value">Артикул</label>

                    <div class="col-sm-3">
                        <input type="text" id="value" ng-model="goods.article" class="form-control" placeholder=""/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="model">Модель</label>

                    <div class="col-sm-10">
                        <input type="text" id="model" ng-model="goods.model" class="form-control"
                               placeholder="Название модели"/>
                    </div>
                </div>

                <div class="form-group" ng-if="vendors.length!=0">
                    <label class="col-sm-2 control-label" for="vendor">Производитель</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="goods.vendor_id" style="width: 100%">
                            <option title="{{v.name}}" ng-repeat="v in vendors" value="{{v.vendor_id}}">
                                {{v.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group" ng-if="providers.length!=0">
                    <label class="col-sm-2 control-label" for="providers">Поставщик</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="goods.provider_id" style="width: 100%">
                            <option title="{{v.name}}" ng-repeat="v in providers" value="{{v.id}}">
                                {{v.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="goods.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="brief">Краткое описание</label>

                    <div class="col-sm-10">
                        <textarea id="brief" ng-model="goods.brief" class="form-control"></textarea>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="value">Цена</label>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" id="value" ng-model="goods.value" class="form-control" placeholder="0"/>
                            <span class="input-group-addon"><a
                                    href="<?= link_to('admin_currency', array('action' => 'list')); ?>">у.е.</a></span>
                        </div>
                    </div>

                    <div class="col-sm-3">
                        <select ng-model="goods.code" class="form-control before_select2  before_select_value"
                                data-value="{{goods.code}}">
                            <option title="{{c.name}}" ng-repeat="c in currency" value="{{c.code}}">
                                {{c.name}} {{c.code}}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" ng-model="goods.value_of" ng-true-value="1" ng-false-value="0"/>
                                от
                            </label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="value">Цена старая</label>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" id="value" ng-model="goods.value_old" class="form-control"
                                   placeholder="0"/>
                            <span class="input-group-addon"><a
                                    href="<?= link_to('admin_currency', array('action' => 'list')); ?>">у.е.</a></span>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="minimum">Минимальный заказ</label>

                    <div class="col-sm-3">
                        <input type="text" id="minimum" ng-model="goods.minimum" class="form-control"
                               placeholder="1"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="value">Остаток</label>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" id="value" ng-model="goods.amount" class="form-control" placeholder="0"/>
                        </div>
                    </div>

                    <div class="col-sm-2">
                        <select ng-model="goods.unit_id" class="form-control chara_goods"
                                data-value="{{goods.unit_id}}">
                            <option value="">--Выберите ед.изм.--</option>
                            <option title="{{u.name}}" ng-repeat="u in unit" value="{{u.unit_id}}">
                                {{u.abbr}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Позиция</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="goods.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Не обязательное поле</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс шаблона</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="goods.prefix" class="form-control"
                               placeholder="shop/goods/show_*"/>

                        <p class="help-block">Для выбора не стандартного отображения товара shop/goods/show_{prefix}</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="video_id">Видео</label>

                    <div class="col-sm-10">
                        <input type="text" id="video_id" ng-model="goods.video_id" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">ID video [<a target="_blank" href="<?= link_to('admin_video')?>">список</a>]</span>
                    </div>
                </div>

            </div>
            <!-- #goods -->
            <div class="tab-pane" id="chara">
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

                        <a ng-if="item.main == 0" ng-click="item.main = 1" class="btn btn-default"
                           href="javascript:;"><i
                                    class="glyphicon glyphicon-star"></i></a>
                        <a ng-if="item.main == 1" ng-click="item.main = 0" class="btn btn-warning"
                           href="javascript:;"><i
                                    class="glyphicon glyphicon-star"></i></a>
                    </div>

                    <div class="col-sm-1">
                        <a ng-click="deleteChara(item.inc)" class="btn btn-danger" href="javascript:void(0)"><i
                                class="glyphicon glyphicon-trash"></i></a>
                    </div>

                </div>


                <div class="form-group">
                    <div class=" col-sm-10">
                        <a href="javascript:;" ng-click="addChara()" class="btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-plus"></i>
                            Добавить характеристику
                        </a>
                    </div>
                </div>


            </div>
            <!-- #chara -->
            <div class="tab-pane" id="attr">

                <div ng-hide="attr.delete" id="inc_{{attr.inc}}" class="form-group" ng-repeat=" attr in attributes">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name_{{attr.inc}}">Название</label>

                        <div class="col-sm-10">
                            <input type="text" id="name_{{attr.inc}}" ng-model="attr.name" class="form-control"
                                   placeholder="Название атрибута"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="value_{{attr.inc}}">Значние</label>

                        <div class="col-sm-10">
                            <input type="text" id="value_{{attr.inc}}" ng-model="attr.value" class="form-control"
                                   placeholder="Значаение атрибута"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="pos_{{attr.inc}}">Позиция</label>

                        <div class="col-sm-10">
                            <input type="text" id="pos_{{attr.inc}}" ng-model="attr.pos" class="form-control"
                                   style="width: 50px;"
                                   placeholder="0" parser-int/>
                            <span class="help-block">Не обязательное поле</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="pos_{{attr.inc}}"></label>

                        <div class="col-sm-10">
                            <button ng-click="deleteAttr(attr.inc)" class="btn btn-xs btn-danger" type="button">
                                <i class="glyphicon glyphicon-trash"></i>
                                Удалить атрибут
                            </button>

                        </div>
                    </div>
                    <hr style="border: 1px solid #000"/>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="subject"></label>

                    <div class="col-sm-10">
                        <a href="javascript:;" ng-click="addAttr()" class="btn btn-info btn-xs">
                            <i class="glyphicon glyphicon-plus"></i>
                            Добавить атрибут
                        </a>
                    </div>
                </div>

            </div>
            <!-- #attr -->

            <div class="tab-pane" id="desc">

                <div ng-hide="sec.delete" id="inc_{{sec.inc}}" class="form-group" ng-repeat=" sec in sections">

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="subject">Статус:</label>

                        <div class="col-sm-10">
                            <label class="checkbox">
                                <input type="checkbox" ng-model="sec.status" ng-true-value="'1'" ng-false-value="'0'"/>
                                <span ng-if="sec.status == 1 ">Вкл</span>
                                <span ng-if="sec.status == 0 ">Выкл</span>
                            </label>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name_{{sec.inc}}">Название</label>

                        <div class="col-sm-10">
                            <input type="text" id="name_{{sec.inc}}" ng-model="sec.name" class="form-control"
                                   placeholder="Название блока"/>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="text_{{sec.inc}}">Описание товара</label>

                        <div class="col-sm-10">
                <textarea name="text_{{sec.inc}}" id="text_{{sec.inc}}" ng-model="sec.text"
                          class="form-control"></textarea>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="pos_{{sec.inc}}">Позиция</label>

                        <div class="col-sm-10">
                            <input type="text" id="pos_{{sec.inc}}" ng-model="sec.pos" class="form-control"
                                   style="width: 50px;"
                                   placeholder="0" parser-int/>
                            <span class="help-block">Не обязательное поле</span>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="pos_{{sec.inc}}"></label>

                        <div class="col-sm-10">
                            <button ng-click="deleteSection(sec.inc)" class="btn btn-xs btn-danger" type="button">
                                <i class="glyphicon glyphicon-trash"></i>
                                Удалить раздел
                            </button>

                        </div>
                    </div>
                    <hr style="border: 1px solid #000"/>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="subject"></label>

                    <div class="col-sm-10">
                        <a href="javascript:void(0)" ng-click="addSection()" class="btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-plus"></i>
                            Добавить раздел
                        </a>
                    </div>
                </div>


            </div>
            <!-- #desc -->

            <div class="tab-pane" id="meta">
                <fieldset>

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

                </fieldset>
            </div>
            <!-- #meta -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Добавить данные к выбраным товарам</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._ids = '<?= $ids?>';
    window._type_id= <?= $type_id?>;
    window._goods_types = <?= $goods_types? \Delorius\Utils\Json::encode((array)$goods_types): '[]'?>;
    window._chara = <?= $chara? \Delorius\Utils\Json::encode((array)$chara): '[]'?>;
    window._chara_values = <?= $chara_values? \Delorius\Utils\Json::encode((array)$chara_values): '[]'?>;
    window._unit = <?= $unit? \Delorius\Utils\Json::encode((array)$unit): '[]'?>;
    window._vendors = <?= $vendors? \Delorius\Utils\Json::encode((array)$vendors): '[]'?>;
    window._providers = <?= $providers ? \Delorius\Utils\Json::encode((array)$providers) : '[]'?>;
    window._currency = <?= $currency? \Delorius\Utils\Json::encode((array)$currency): '[]'?>;

    window._categories = <?= $this->action('Shop:Admin:Category:catsJson',array('pid'=>0,'typeId'=>$type_id,'placeholder'=>' '));?>;

    $(function () {
        $('#myTab').tab();
    });

    $(function () {
        $('.image-link').magnificPopup({
            type: 'image',
            gallery: {
                enabled: true
            }
        });
    });
</script>





