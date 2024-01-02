<div ng-controller="GoodsOptionEditCtrl" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад к товару" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'edit', 'id' => $goods['goods_id'])) ?>">
            <i class="glyphicon glyphicon-arrow-left"></i> Назад к товару
        </a>
        <a title="Список опций" class="btn btn-info btn-xs"
           href="<?= link_to('admin_option', array('action' => 'list', 'id' => $goods['goods_id'])) ?>">
            <i class="glyphicon glyphicon-th"></i> Список опций
        </a>
    </div>

    <h1>Опция</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#option" data-toggle="tab">Описание</a></li>
        <li ng-show="show_variants()"><a href="#variants" data-toggle="tab">Вариант</a></li>
    </ul>


    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="option">

                <div class="form-group">
                    <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="option.status" ng-true-value="'1'" id="inputstatus"
                                   ng-false-value="'0'"/> Показать</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="option.name" class="form-control"
                               placeholder="Название"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="pos">Приоритет</label>

                    <div class="col-sm-10">
                        <input type="text" id="pos" ng-model="option.pos" class="form-control"
                               style="width: 80px;text-align: center;"
                               placeholder="0" parser-int/>
                    </div>
                </div>

                <div class="form-group" ng-if="!is_text()">
                    <label for="inventory" class="col-sm-2 control-label">Инвентаризация</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="option.inventory" ng-true-value="'1'" id="inventory"
                                   ng-false-value="'0'"/> Учитывать при расчете комбинаций</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="type">Тип</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="option.type" id="type" style="width: 100%">
                            <option value="{{t.id}}" ng-repeat="t in types">{{t.name}}</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="inventory" class="col-sm-2 control-label">Обязательное</label>

                    <div class="col-sm-10">
                        <p class="form-control-static">
                            <input type="checkbox" ng-model="option.required" ng-true-value="'1'" id="inventory"
                                   ng-false-value="'0'"/></p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="text">Описание</label>

                    <div class="col-sm-10">
                        <textarea id="text" ng-model="option.text" class="form-control"></textarea>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="comment">Комментарий</label>

                    <div class="col-sm-10">
                        <input type="text" id="comment" ng-model="option.comment" class="form-control"/>

                        <p class="help-block">Введите комментарий, который будет показываться под опцией</p>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="prefix">Шаблон для вариантов</label>

                    <div class="col-sm-10">
                        <input type="text" id="prefix" ng-model="option.prefix" class="form-control"/>
                    </div>
                </div>

            </div>
            <!-- #option -->

            <div class="tab-pane" id="variants">

                <!-- flag -->
                <table class="table table-condensed table-bordered table-hover" ng-if="is_flag()">
                    <tr>
                        <th width="20">ID</th>
                        <th width="200">Модификатор</th>
                        <th></th>
                    </tr>
                    <tr ng-repeat="variant in variants" ng-if="variant.pos == 1">
                        <td class="i-center-td">{{variant.id}}</td>
                        <td class="i-center-td">
                            <input style="text-align: right;" name="modifier" ng-model="variant.modifier"
                                   class="form-control" ng-blur="edit(variant)"/>
                        </td>
                    </tr>
                </table>
                <!-- /flag -->

                <!-- select -->
                <table class="table table-condensed table-bordered table-hover" ng-if="!is_flag()">
                    <tr>
                        <th width="20">ID</th>
                        <th class="i-center-td" width="20"><i class="glyphicon glyphicon-eye-open"></i></th>
                        <th class="i-center-td" width="55">Фото</th>
                        <th>Название</th>
                        <th width="100">Модификатор</th>
                        <th class="i-center-td" width="70">n/%</th>
                        <th width="100">Код</th>
                        <th class="i-center-td" width="50"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i>
                        </th>
                        <th width="20"></th>
                    </tr>
                    <tr>
                        <td></td>
                        <td></td>
                        <td></td>
                        <td><input name="name" ng-model="form.name" class="form-control"/></td>
                        <td><input name="modifier" ng-model="form.modifier" class="form-control"/></td>
                        <td>
                            <select name="type" ng-model="form.type" class="form-control"
                                    ng-options="key as value for (key,value) in variant_types"></select>
                        </td>
                        <td><input name="external_id" ng-model="form.external_id" class="form-control"/></td>
                        <td><input name="pos" ng-model="form.pos" class="form-control "/></td>
                        <td>
                            <a title="Добавить" class="btn btn-xs btn-success" ng-click="addVariant()"
                               href="javascript:;">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                        </td>
                    </tr>
                    <tr ng-repeat="variant in variants">
                        <td class="i-center-td">{{variant.id}}</td>
                        <td class="i-center-td">
                            <input type="checkbox" name="name"
                                   ng-model="variant.status"
                                   ng-true-value="'1'"
                                   ng-false-value="'0'"
                                   ng-click="edit(variant)"
                                   style="cursor: pointer;"/>
                        </td>
                        <td>
                            <label class="b-input-upload" for="image_{{variant.id}}">
                                <img ng-src="{{getImageSrc(variant.id)}}" alt=""/>
                                <input id="image_{{variant.id}}" type="file" title="Загрузить фото"
                                       ng-file-select="onFileSelect($files,variant.id)"/>
                            </label>
                        </td>

                        <td class="i-center-td">
                            <input name="name" ng-model="variant.name" class="form-control"
                                   ng-blur="edit(variant)"/>
                        </td>
                        <td class="i-center-td">
                            <input style="text-align: right;" name="modifier"
                                   ng-model="variant.modifier" class="form-control"
                                   ng-blur="edit(variant)"/>
                        </td>
                        <td class="i-center-td">
                            <select name="type" ng-model="variant.type" class="form-control" ng-blur="edit(variant)"
                                    ng-options="key as value for (key,value) in variant_types"></select>
                        </td>
                        <td class="i-center-td">
                            <input style="text-align: right;" name="external_id"
                                   ng-model="variant.external_id" class="form-control"
                                   ng-blur="edit(variant)"/>
                        </td>
                        <td class="i-center-td">
                            <input name="pos" ng-model="variant.pos"
                                   class="form-control text-center " ng-blur="edit(variant)"/>
                        </td>
                        <td class="i-center-td">

                            <div class="btn-group">
                                <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                                    <i class="glyphicon glyphicon-cog"></i>
                                </a>
                                <ul class="dropdown-menu pull-right">
                                    <li ng-if="getImageId(variant.id) !=0">
                                        <a href="<?= link_to('admin_image', array('action' => 'edit')) ?>?id={{getImageId(variant.id)}}"><i
                                                class="glyphicon glyphicon-camera"></i> Редактировать картинку</a>
                                    </li>
                                    <li>
                                        <a href="<?= link_to('admin_option', array('action' => 'variant')) ?>?id={{variant.id}}"><i
                                                class="glyphicon glyphicon-edit"></i> Редактировать</a>
                                    </li>
                                    <li>
                                        <a tabindex="-1" href="javascript:;" ng-click="delete(variant.id);">
                                            <i class="glyphicon glyphicon-trash"></i> Удалить
                                        </a>
                                    </li>
                                </ul>
                            </div>
                        </td>
                    </tr>
                </table>
                <!-- /select -->
            </div>
            <!-- #variants -->

        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._option = <?= $option? \Delorius\Utils\Json::encode((array)$option): '{type:1}'?>;
    window._goods = <?= $goods? \Delorius\Utils\Json::encode((array)$goods): '{}'?>;
    window._variants = <?= $variants? \Delorius\Utils\Json::encode((array)$variants): '[]'?>;
    window._types = <?= $types? \Delorius\Utils\Json::encode((array)$types): '[]'?>;
    window._variant_types = <?= $variant_types? \Delorius\Utils\Json::encode((array)$variant_types): '[]'?>;
    window._images = <?= $images? \Delorius\Utils\Json::encode((array)$images): '[]'?>;
</script>





