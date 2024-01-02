<div ng-controller="CollectionEditCtrl" ng-init="init(<?= $reload ?>)">

    <div class="clearfix btn-group ">
        <a title="Назад" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_collection', array('action' => 'list')) ?>">
            <i class=" glyphicon glyphicon-arrow-left"></i>
        </a>
        <a title="Добвить коллекцию" class="btn btn-success btn-xs"
           href="<?= link_to('admin_collection', array('action' => 'add')) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>

    <h1>Коллекция</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#collection" data-toggle="tab">Описание</a></li>
        <li ng-show="collection.id"><a href="#images" data-toggle="tab">Фото</a></li>
        <li><a href="#complect" data-toggle="tab" title="Комплектность">Комплектность</a></li>
        <li><a href="#goods" data-toggle="tab" title="Добавить товар в коллекцию">Товар</a></li>
        <li><a href="#attr" data-toggle="tab" title="Атрибуты">Атрибуты</a></li>
        <li><a href="#meta" data-toggle="tab" title="Характиристики">SEO параметры</a></li>
    </ul>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">

            <div class="tab-pane active" id="collection">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="collection.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать коллекцию</p>
                    </div>
                </div>

                <div class="form-group" ng-if="show_select_type()">
                    <label class="col-sm-2 control-label" for="name">Тип</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="collection.ctype" ng-change="select_type(collection.ctype)"
                                style="width: 100%">
                            <option value="{{type.id}}" ng-repeat="type in types">{{type.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Категория</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="collection.cid" style="width: 100%">
                            <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                    ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="vendor">Производитель</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="collection.vendor_id" style="width: 100%">
                            <option value="0">Производитель не указан</option>
                            <option title="{{v.name}}" ng-repeat="v in vendors" value="{{v.vendor_id}}">
                                {{v.name}}
                            </option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="collection.name" class="form-control"
                               placeholder="Название коллекции"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="short_name">Коротное название</label>

                    <div class="col-sm-10">
                        <input type="text" id="short_name" ng-model="collection.short_name" class="form-control"
                               placeholder="Коротное название коллекции"/>
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
                    <label class="col-sm-2 control-label" for="text">Описание</label>

                    <div class="col-sm-10">
                        <textarea id="text" ng-model="collection.text" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="article">Артикул</label>

                    <div class="col-sm-3">
                        <input type="text" id="article" ng-model="collection.article" class="form-control"
                               placeholder=""/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Позиция</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="collection.pos" class="form-control" style="width: 50px;"
                               placeholder="0" parser-int/>
                        <span class="help-block">Необязательное поле</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс шаблона</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="collection.prefix" class="form-control"
                               placeholder="shop/collection/show_*"/>
                        <p class="help-block">
                            Для выбора не стандартного отображения коллекции shop/collection/show_{prefix}
                        </p>
                    </div>
                </div>

            </div>
            <!-- #collection -->

            <div ng-show="collection.id" class="tab-pane" id="images">
                <div class="clearfix">
                    Добавить фото: <input type="file" ng-file-select="onFileSelect($files,collection.id)" multiple/>
                </div>
                <br clear="all"/><br clear="all"/>

                <table class="table table-condensed table-bordered table-hover table-middle">
                    <tr>
                        <th>Фото</th>
                        <th>Название</th>
                        <th width="80">Приоритет</th>
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
                            <input id="og_title" ng-model="meta.options.og.title" class="form-control" />
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="og_description">og:description</label>

                        <div class="col-sm-10">
                            <textarea id="og_description" ng-model="meta.options.og.description" class="form-control" onblur=""></textarea>
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="og_image">og:image</label>

                        <div class="col-sm-10">
                            <input id="og_image" ng-model="meta.options.og.image" class="form-control" />
                        </div>
                    </div>

                </fieldset>
            </div>
            <!-- #meta -->

            <div class="tab-pane" id="goods">
                <h3>Товар в данной коллекции:</h3>

                <div class="clearfix btn-group " style="margin-bottom: 20px">
                    <a title="Добвить товар" class="btn btn-success btn-xs popup-link-ajax"
                       href="<?= link_to('admin_goods_data', array('action' => 'goodsList')) ?>?type_id={{collection.ctype}}">
                        <i class="glyphicon glyphicon-plus"></i>
                    </a>
                </div>
                <table class="table table-condensed table-bordered table-hover table-middle">
                    <tr>
                        <th>Название</th>
                        <th width="200">Комплектность</th>
                        <th width="70">Приоритет</th>
                        <th width="20"></th>
                    </tr>
                    <tr ng-hide="item.delete" ng-repeat="item in packages_goods">
                        <td>
                            {{getNameGoods(item.goods_id)}}
                        </td>
                        <td>
                            <select ui-select2 ng-model="item.package_id" style="width: 100%">
                                <option value="0">Без категории</option>
                                <option ng-if="p.id" value="{{p.id}}" ng-repeat="p in packages">{{p.name}}</option>
                            </select>
                        </td>
                        <td class="i-center-td">
                            <input ng-model="item.pos" class="pos"/>
                        </td>
                        <td>
                            <a class="btn btn-xs btn-danger" href="javascript:;" ng-click="deleteGoods(item.inc)">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </td>
                    </tr>
                </table>

            </div>
            <!-- #goods -->

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
                        <a href="javascript:void(0);" ng-click="addAttr()" class="btn btn-info btn-xs">
                            <i class="glyphicon glyphicon-plus"></i>
                            Добавить атрибут
                        </a>
                    </div>
                </div>

            </div>
            <!-- #attr -->

            <div class="tab-pane" id="complect">


                <table class="table table-condensed table-bordered table-hover">
                    <tr>
                        <th>Название</th>
                        <th width="70">Приоритет</th>
                        <th width="20"></th>
                    </tr>
                    <tr>
                        <td><input name="name" ng-model="form_pack.name" class="form-control"/></td>
                        <td class="i-center-td"> <input name="pos" ng-model="form_pack.pos" class="pos"/></td>
                        <td>
                            <a title="Добавить" class="btn btn-xs btn-success" ng-click="addPackage(form_pack)"
                               href="javascript:;">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                        </td>
                    </tr>
                    <tr ng-repeat="pack in packages" ng-hide="pack.delete">
                        <td class="i-center-td">
                            <input name="name" ng-model="pack.name" class="form-control"/>
                        </td>
                        <td class="i-center-td">
                            <input name="pos" ng-model="pack.pos" class="pos"/>
                        </td>
                        <td class="i-center-td">

                            <a href="javascript:;" ng-click="deletePackage(pack.inc);" class="btn btn-danger btn-xs">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>

                        </td>
                    </tr>
                </table>

            </div>
            <!-- #complect -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._collection = <?= $collection? \Delorius\Utils\Json::encode((array)$collection): '{vendor_id:0,cid:0,status:"0",ctype:"'. ($type_id? (int)$type_id: \Shop\Catalog\Entity\Category::TYPE_GOODS).'"}'?>;
    window._meta = <?= $meta? \Delorius\Utils\Json::encode((array)$meta): '{}'?>;
    window._types = <?= $types? \Delorius\Utils\Json::encode((array)$types): '[]'?>;
    window._images = <?= $images? \Delorius\Utils\Json::encode((array)$images): '[]'?>;
    window._vendors = <?= $vendors? \Delorius\Utils\Json::encode((array)$vendors): '[]'?>;
    window._attributes = <?= $attributes? \Delorius\Utils\Json::encode((array)$attributes): '[]'?>;
    window._packages = <?= $packages? \Delorius\Utils\Json::encode((array)$packages): '[]'?>;
    window._packages_goods = <?= $packages_goods? \Delorius\Utils\Json::encode((array)$packages_goods): '[]'?>;
    window._goods = <?= $goods? \Delorius\Utils\Json::encode((array)$goods): '[]'?>;

    window._categories = <?= $this->action('Shop:Admin:Category:catsJson',array('pid'=>0,'typeId'=>($type_id)? (int)$type_id: \Shop\Catalog\Entity\Category::TYPE_GOODS,'placeholder'=>'Без категории'));?>;

</script>





