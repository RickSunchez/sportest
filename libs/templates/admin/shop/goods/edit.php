<div ng-controller="GoodsEditController" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'list', 'type_id' => $type_id)) ?>">
            <i class=" glyphicon glyphicon-arrow-left"></i>
        </a>
        <a title="Добвить товар" class="btn btn-success btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'add')) ?>?cid={{goods.cid}}">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>

    <h1>Товар <i style="color: #e38d13;font-size: 12px;cursor: pointer;" title="Требуется модерация"
                 ng-show="goods.moder == 1" class="glyphicon glyphicon-pencil"></i></h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#goods" data-toggle="tab">Товар</a></li>
        <li><a href="#chara" data-toggle="tab" title="Характиристики">Хар-ки</a></li>
        <li ng-show="goods.goods_id"><a href="#images" data-toggle="tab">Фото</a></li>
        <li><a href="#desc" data-toggle="tab">Описание</a></li>
        <li ng-show="goods.goods_id"><a
                    href="<?= link_to('admin_option', array('action' => 'list')) ?>?id={{goods.goods_id}}">Опции</a>
        </li>
        <li ng-show="goods.goods_id" class="dropdown">
            <a href="#dop" class="dropdown-toggle" data-toggle="dropdown" href="#">Дополнительно <b
                        class="caret"></b></a>
            <ul aria-labelledby="dop" role="menu" class="dropdown-menu">
                <li ng-show="goods.goods_id">
                    <a href="#acco" data-toggle="tab" title="Добавить сопуствующий товар">
                        Добавить сопуствующий товар
                    </a>
                </li>
                <li>
                    <a href="#types" data-toggle="tab">Тип товара</a>
                </li>
                <li>
                    <a href="#attr" data-toggle="tab">Атрибуты</a>
                </li>
                <li>
                    <a href="#meta" data-toggle="tab">SEO параметры</a>
                </li>
                <li>
                    <a href="#import" data-toggle="tab">Синхронизация</a>
                </li>
                <li>
                    <a ng-click="copyGoods()" data-toggle="tab" tabindex="-1" href="#">Скопировать товар (без фото)</a>
                </li>
            </ul>
        </li>
    </ul>


    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="goods">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="goods.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать товар в магазине</p>
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

                <? if ($multi): ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">Категории </label>

                        <div class="col-sm-10">

                            <div ng-hide="item.delete" id="inc_{{item.inc}}" ng-repeat="item in cats">

                                <a style="margin-top: 10px;" href="javascript:;" ng-click="deleteCat(item.inc)"
                                   class="btn btn-danger btn-xs">
                                    <i class="glyphicon glyphicon-minus"></i>
                                </a>

                                <select ui-select2 ng-model="item.cid" style="width: 90%">
                                    <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                            ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                                    </option>
                                </select>

                            </div>


                            <a style="margin-top: 10px;" href="javascript:;" ng-click="addCat()"
                               class="btn btn-success btn-xs">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                        </div>
                    </div>

                <? else: ?>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="name">Категория <img style="width: 20px"
                                                                                        src="/source/images/load.svg"
                                                                                        alt=""
                                                                                        class="category_load "></label>

                        <div class="col-sm-10">
                            <select ui-select2 ng-model="goods.cid" style="width: 100%">
                                <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                        ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                                </option>
                            </select>
                        </div>
                    </div>

                <? endif ?>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="goods.name" class="form-control"
                               placeholder="Название товара"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="short_name">Коротное название</label>

                    <div class="col-sm-10">
                        <input type="text" id="short_name" ng-model="goods.short_name" class="form-control"
                               placeholder="Коротное название товара"/>
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
                            <option value="0">Производитель не указан</option>
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
                            <option value="0">Поставщик не указан</option>
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
                    <label class="col-sm-2 control-label" for="value">Вес (кг)</label>

                    <div class="col-sm-3">
                        <input type="text" id="weight" ng-model="goods.weight" class="form-control" placeholder=""/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="value">Цена</label>

                    <div class="col-sm-3">
                        <div class="input-group">
                            <input type="text" id="value" ng-model="goods.value" class="form-control" placeholder="0"/>
                            <span class="input-group-addon">{{goods.code}}</span>
                        </div>
                    </div>
                    <div class="col-sm-3">
                        <select ui-select2 ng-model="goods.code" style="width: 100%">
                            <option title="{{c.name}}" ng-repeat="c in currency" value="{{c.code}}">
                                {{c.name}} {{c.code}}
                            </option>
                        </select>
                    </div>
                    <div class="col-sm-3">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" ng-model="goods.value_of" ng-true-value="'1'"
                                       ng-false-value="'0'"/>
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
                            <span class="input-group-addon">{{goods.code}}</span>
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
                    <label class="col-sm-2 control-label" for="maximum">Максимальный заказ</label>

                    <div class="col-sm-3">
                        <input type="text" id="maximum" ng-model="goods.maximum" class="form-control"
                               placeholder="1"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="step">Шаг заказ</label>

                    <div class="col-sm-3">
                        <input type="text" id="step" ng-model="goods.step" class="form-control"
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
                        <select ui-select2 ng-model="goods.unit_id" class="form-control">
                            <option value="0">--Eд.изм.--</option>
                            <option title="{{u.name}}" ng-repeat="u in unit" value="{{u.unit_id}}">
                                {{u.abbr}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="goods.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Необязательное поле</span>
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
                        <input type="text" id="video_id" ng-model="goods.video_id" class="form-control"
                               style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">ID video [<a target="_blank" href="<?= link_to('admin_video') ?>">список</a>]</span>
                    </div>
                </div>


            </div>
            <!-- #goods -->

            <div class="tab-pane" id="types">
                <fieldset>
                    <legend>Типы</legend>

                    <div class=" col-sm-10 col-sm-offset-2 checkbox" ng-repeat="type in types">
                        <label>
                            <input type="checkbox"
                                   ng-model="type.status"
                                   ng-true-value="1"
                                   ng-false-value="0"> {{type.name}}
                        </label>
                    </div>
                    <br/>
                </fieldset>
                <br/><br/>
            </div>
            <!-- #types -->


            <div class="tab-pane" id="acco">
                <h3>С этим товаром покупают:</h3>

                <div class="clearfix btn-group " style="margin-bottom: 20px">
                    <a title="Добвить товар" class="btn btn-success btn-xs popup-link-ajax"
                       href="<?= link_to('admin_goods_data', array('action' => 'goodsList')) ?>?type_id={{goods.ctype}}">
                        <i class="glyphicon glyphicon-plus"></i>
                    </a>
                    <a title="Копировать товары" class="btn btn-warning btn-xs popup-link-ajax"
                       href="<?= link_to('admin_goods_data', array('action' => 'goodsListAds')) ?>?type_id={{goods.ctype}}&gid={{goods.goods_id}}">
                        <i class="glyphicon glyphicon-copy"></i>
                    </a>
                </div>
                <table class="table table-condensed table-bordered table-hover table-middle">
                    <tr>
                        <th>Название</th>
                        <th width="200">Тип</th>
                        <th width="75">Позиция</th>
                        <th width="20"></th>
                    </tr>
                    <tr ng-repeat="item in goods_accompanies">
                        <td>
                            {{getNameGoods(item.target_id)}}
                        </td>
                        <td>
                            <select ng-model="item.type_id" ui-select2 style="width: 200px;"
                                    ng-change="changeAcco(item)">
                                <option title="{{a_t.name}}" ng-repeat="a_t in accs_types" value="{{a_t.id}}">
                                    {{a_t.name}}
                                </option>
                            </select>
                        </td>
                        <td class="i-center-td">
                            <input ng-model="item.pos" class="pos" ng-change="changeAcco(item)"/>
                        </td>
                        <td>
                            <a class="btn btn-xs btn-danger" href="javascript:;" ng-click="deleteAcco(item.id)">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </td>
                    </tr>
                </table>
            </div>
            <!-- #acco -->


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
                        <i title="Добавить значение" class="glyphicon glyphicon-plus btn_add-value"
                           ng-click="addValueForm(item.inc)"></i>

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
                        <a ng-click="deleteChara(item.inc)" class="btn btn-danger " href="javascript:;"><i
                                    class="glyphicon glyphicon-trash"></i></a>
                    </div>
                </div>

                <div class="form-group">
                    <div class=" col-sm-3">
                        <a href="javascript:;" ng-click="addChara()" class="btn btn-success btn-xs">
                            <i class="glyphicon glyphicon-plus"></i>
                            Добавить характеристику
                        </a>
                    </div>
                    <div class="col-sm-4">
                        <a class="btn btn-primary btn-xs popup-link-ajax"
                           href="<?= link_to('admin_goods_data', array('action' => 'goodsList')) ?>?type_id={{goods.ctype}}&select=goodsChara">
                            <i class="glyphicon glyphicon-import"></i>
                            Скопировать характеристики
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

            <div ng-show="goods.goods_id" class="tab-pane" id="images">
                <div class="clearfix">
                    Добавить фото: <input type="file" ng-file-select="onFileSelect($files,goods.goods_id)" multiple/>
                </div>
                <br clear="all"/><br clear="all"/>
                <table class="table table-condensed table-bordered table-hover table-middle">
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th width="80">Позиция</th>
                        <th>#</th>
                    </tr>
                    <tr ng-repeat="image in images">
                        <td width="60" align="center">
                            <a href="{{image.normal}}" class="image-link">
                                <img width="50" ng-src="{{image.preview}}" alt=""/>
                            </a>
                        </td>
                        <td valign="middle">
                            <div class="input-group">
                                <input name="name" ng-model="image.name" class="form-control"/>
                                <span title="Сохранить название" class="input-group-addon btn-success"
                                      ng-click="saveImage(image)"
                                      style="cursor: pointer;color: #ffffff;">
                                     <i class="glyphicon glyphicon-ok"></i>
                                </span>
                            </div>
                        </td>
                        <td class="i-center-td">
                            <input ng-model="image.pos" class="pos" ng-blur="saveImage(image)"/>
                        </td>
                        <td width="90">
                            <div class="btn-group">
                                <div class="btn-group">
                                    <a href="javascript:;" class="btn btn-danger btn-xs"
                                       ng-click="delete_image(image)">
                                        <i class="glyphicon glyphicon-trash"></i>
                                    </a>
                                    <a ng-if="image.main == '0'" href="javascript:;" class="btn btn-default btn-xs"
                                       ng-click="main_image(image,1)">
                                        <i class="glyphicon glyphicon-star"></i>
                                    </a>
                                    <a ng-if="image.main == '1'" href="javascript:;" class="btn btn-warning btn-xs"
                                       ng-click="main_image(image,0)">
                                        <i class="glyphicon glyphicon-star"></i>
                                    </a>
                                    <a href="<?= link_to('admin_image', array('action' => 'edit')) ?>?id={{image.image_id}}"
                                       title="Редактировать" class="btn btn-info btn-xs">
                                        <i class="glyphicon glyphicon-pencil"></i>
                                    </a>
                                </div>
                            </div>
                        </td>
                    </tr>
                </table>


            </div>
            <!-- #images -->
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
                        <a href="javascript:;" ng-click="addSection()" class="btn btn-success btn-xs">
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

                </fieldset>
            </div>
            <!-- #meta -->


            <div class="tab-pane" id="import">
                <fieldset>
                    <legend>Импорт</legend>

                    <div class="form-group">
                        <label for="inner" class="col-sm-2 control-label">Пространство</label>

                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="checkbox" ng-model="goods.inner" ng-true-value="'1'" id="inner"
                                       ng-false-value="'0'"/> Внутрений товар</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="external_change" class="col-sm-2 control-label">Изменение</label>

                        <div class="col-sm-10">
                            <p class="form-control-static">
                                <input type="checkbox" ng-model="goods.external_change" ng-true-value="'1'"
                                       id="external_change"
                                       ng-false-value="'0'"/> Учитывать при импорте</p>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="external_id">Внешний ИД</label>

                        <div class="col-sm-10">
                            <input type="text" id="external_id" ng-model="goods.external_id" class="form-control"/>
                        </div>
                    </div>

                </fieldset>
            </div>
            <!-- #import -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
                <span ng-show="goods.moder == 1"
                      style="padding-left: 10px;">Внимание! При сохранении пройдет модерацию.</span>
            </div>
        </div>
    </form>


    <div id="form" class="b-popup _form_menu mfp-hide ">
        <div class="title">Добавить значение для характеристики</div>

        <div class="well form-horizontal">

            <div class="form-group">
                <label class="col-sm-2 control-label" for="name">Значение</label>

                <div class="col-sm-10">
                    <input type="text" id="name" ng-model="form.name" class="form-control"
                           placeholder="Значение"/>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="unit_id">Ед. измереи</label>

                <div class="col-sm-10">
                    <p class="form-control-static">
                        <select ui-select2 ng-model="form.unit_id" style="width: 100%;">
                            <option value="0">Без измерений</option>
                            <option value="{{u.unit_id}}" ng-repeat="u in unit">{{u.name}} ({{u.abbr}})</option>
                        </select>
                    </p>
                </div>
            </div>

            <div class="form-group">
                <label class="col-sm-2 control-label" for="code">Код значения:</label>

                <div class="col-sm-10">
                    <input type="text" id="code" ng-model="form.code" placeholder="Код значения:" class="form-control"/>
                </div>
            </div>


            <div class="form-group">
                <label class="col-sm-2 control-label" for="info">Примечание</label>

                <div class="col-sm-10">
                    <textarea id="info" ng-model="form.info" class="form-control" placeholder="Примечание"></textarea>
                </div>
            </div>


            <div class="form-group">
                <div class="col-sm-offset-2 col-sm-10">
                    <button type="submit" ng-click="valueFormSave()" class="btn btn-info">Добавить</button>
                </div>
            </div>

        </div>

    </div>


</div>

<script type="text/javascript">
    window._goods = <?= $goods ? \Delorius\Utils\Json::encode((array)$goods) : '{code:"' . SYSTEM_CURRENCY . '",unit_id:"1",provider_id:0,vendor_id:0,cid:0,status:"1",amount:"1",is_amount:"1",value_of:"0",external_change:"1",inner:"0",minimum:1,step:1,ctype:"' . $type_id . '"}'?>;
    window._meta = <?= $meta ? \Delorius\Utils\Json::encode((array)$meta) : '{}'?>;
    window._sections = <?= $sections ? \Delorius\Utils\Json::encode((array)$sections) : '[]'?>;
    window._cats = <?= $cats ? \Delorius\Utils\Json::encode((array)$cats) : '[]'?>;
    window._goods_types = <?= $goods_types ? \Delorius\Utils\Json::encode((array)$goods_types) : '[]'?>;
    window._accs_types = <?= $accs_types ? \Delorius\Utils\Json::encode((array)$accs_types) : '[]'?>;
    window._types = <?= $types ? \Delorius\Utils\Json::encode((array)$types) : '[]'?>;
    window._list_types = <?= $list_types ? \Delorius\Utils\Json::encode((array)$list_types) : '[]'?>;
    window._groups = <?= $groups ? \Delorius\Utils\Json::encode((array)$groups) : '[]'?>;
    window._chara = <?= $chara ? \Delorius\Utils\Json::encode((array)$chara) : '[]'?>;
    window._chara_goods = <?= $chara_goods ? \Delorius\Utils\Json::encode((array)$chara_goods) : '[]'?>;
    window._chara_values = <?= $chara_values ? \Delorius\Utils\Json::encode((array)$chara_values) : '[]'?>;
    window._attributes = <?= $attributes ? \Delorius\Utils\Json::encode((array)$attributes) : '[]'?>;
    window._accompanies = <?= $accompanies ? \Delorius\Utils\Json::encode((array)$accompanies) : '[]'?>;
    window._goods_accompanies = <?= $goods_accompanies ? \Delorius\Utils\Json::encode((array)$goods_accompanies) : '[]'?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode((array)$images) : '[]'?>;
    window._unit = <?= $unit ? \Delorius\Utils\Json::encode((array)$unit) : '[]'?>;
    window._currency = <?= $currency ? \Delorius\Utils\Json::encode((array)$currency) : '[]'?>;
    window._vendors = <?= $vendors ? \Delorius\Utils\Json::encode((array)$vendors) : '[]'?>;
    window._providers = <?= $providers ? \Delorius\Utils\Json::encode((array)$providers) : '[]'?>;
    window._cid = <?= $cid ? $cid : '0'?>;
</script>


<style type="text/css">
    .btn_add-value {
        position: absolute;
        right: -5px;
        top: 10px;
        cursor: pointer;
    }
</style>






