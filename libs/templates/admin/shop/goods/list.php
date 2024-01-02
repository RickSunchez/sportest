<div ng-controller="GoodsListCtrl" ng-init='init()'>
    <div class="clearfix btn-group">
        <a title="Добвить товар" class="btn btn-success btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'add', 'type_id' => $type_id)) ?>">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
        <button title="Поиск" ng-click="form_search = 1" type="button" class="btn btn-info btn-xs"><i
                    class="glyphicon glyphicon-search"></i></button>
        <a title="Товар требующие модерации" class="btn btn-warning btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'list', 'moder' => 1, 'type_id' => $type_id)) ?>">
            <i class="glyphicon glyphicon-question-sign"></i>
        </a>
        <a title="Отключиные товары" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'list', 'status' => 0, 'type_id' => $type_id)) ?>">
            <i class="glyphicon glyphicon-eye-close"></i>
        </a>
        <a title="Связи товаров" class="btn btn-primary btn-xs" style="float: right"
           href="<?= link_to('admin_product_collection', array('action' => 'list', 'type_id' => $type_id)) ?>">
            <i class="glyphicon glyphicon-option-horizontal"></i>
        </a>
    </div>

    <form class="well" role="form " style="width: 400px;margin-top: 40px;" ng-show="form_search">
        <fieldset>
            <legend>Поиск</legend>
            <div class="form-group">
                <label for="inputstatus">Статус:</label>
                <select ng-model="get.status" class="form-control">
                    <option value="">Все</option>
                    <option value="1">Активны</option>
                    <option value="0">Отключены</option>
                </select>
            </div>
            <div class="form-group" ng-if="show_select_type()">
                <label for="inputtype">Тип:</label>
                <select ui-select2 ng-model="get.type_id" ng-change="select_type(get.type_id)" style="width: 200px;">
                    <option value="0">Все</option>
                    <option value="{{type.id}}" ng-repeat="type in goods_types">{{type.name}}</option>
                </select>
            </div>
            <div class="form-group">
                <label for="inputstatus">{{get}} Категория: <img style="width: 20px" src="/source/images/load.svg" alt="" class="category_load "></label>
                <select ui-select2 ng-model="get.cid" style="width: 100%">
                    <option value="-1">Без категории</option>
                    <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                            ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                    </option>
                </select>
            </div>
            <div class="form-group">
                <label for="inputarticle">Артикул товара</label>
                <input ng-model="get.article" class="form-control" id="inputarticle" placeholder="">
            </div>
            <div class="form-group">
                <label for="inputname">Название товара</label>
                <input ng-model="get.name" class="form-control" id="inputname" placeholder="">
            </div>
            <div class="checkbox">
                <label>
                    <input type="checkbox" ng-model="get.moder" ng-true-value="'1'" ng-false-value="'0'"> на модерации
                </label>
            </div>
            <div class="form-group">
                <label for="inputstep">Кол-во товаров на стр.</label>
                <input ng-model="get.step" class="form-control" id="inputstep" placeholder="20">
            </div>
            <button ng-click="search()" type="button" class="btn btn-success">Искать</button>
            <button ng-click="cancel()" type="button" class="btn btn-default">Отмена</button>
        </fieldset>
    </form>

    <br clear="all"/>
    <br/>

    <div>
        <div>Кол-во товаров: <?= $pagination->getItemCount() ?></div>
    </div>

    <table class="table table-condensed table-bordered table-hover table-middle table-edit">
        <tr>
            <th width="20">
                <input class="goods_select_all" type="checkbox" ng-click="selectAll()"/>
            </th>
            <th width="20">ID</th>
            <th class="i-center-td" width="55">Фото</th>
            <th>Название</th>
            <th width="50">Наличие</th>
            <th class="text-right" width="100">Цена</th>
            <th class="text-right" width="100">Старая</th>
            <th class="text-center" width="100">Валюта</th>
            <th class="i-center-td" width="40"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i></th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in goods">
            <td><input class="goods_ids" type="checkbox" value="{{item.goods_id}}" name="ids[]"/></td>
            <td>{{item.goods_id}}</td>
            <td>
                <label class="b-input-upload" for="img_{{item.goods_id}}">
                    <img width="50" ng-src="{{getImageSrc(item.goods_id)}}" alt=""/>
                    <input id="img_{{item.goods_id}}" type="file" ng-file-select="onFileSelect($files,item.goods_id)"
                           title="Загрузить фото"/>
                </label>
            </td>
            <td>
                <div ng-if="item.article">Артикул: {{item.article}}</div>
                <a href="<?= link_to('admin_goods', array('action' => 'edit')) ?>?id={{item.goods_id}}">
                    <span ng-show="item.status == 1"> {{item.name}}</span>
                    <s ng-show="item.status == 0">{{item.name}}</s>
                    <span class="popular">{{item.popular}}</span>
                </a>


                <i style="color: #e38d13;font-size: 12px;cursor: pointer;" title="Требуется модерация"
                   ng-show="item.moder == 1" class="glyphicon glyphicon-pencil"></i>

            </td>
            <td><input ng-model="item.amount" class="form-control text-right" ng-blur="saveGoods(item)"/>
            </td>
            <td><input ng-model="item.value" class="form-control text-right" ng-blur="saveGoods(item)"/>
            </td>
            <td><input ng-model="item.value_old" class="form-control text-right" ng-blur="saveGoods(item)"/>
            </td>
            <td>
                <select ng-change="saveGoods(item)" ng-model="item.code"
                        class="form-control before_select2  before_select_value"
                        data-value="{{item.code}}">
                    <option title="{{c.name}}" ng-repeat="c in currency" value="{{c.code}}">
                        {{c.name}} {{c.code}}
                    </option>
                </select>
            </td>
            <td class="i-center-td">
                <input ng-model="item.pos" class="pos" ng-blur="change_pos(item)"/>
            </td>
            <td>
                <div class="btn-group">
                    <a class="btn btn-xs btn-info dropdown-toggle " data-toggle="dropdown" href="#">
                        <i class="glyphicon glyphicon-cog"></i>
                    </a>
                    <ul class="dropdown-menu pull-right">
                        <li>
                            <a href="<?= link_to('admin_goods', array('action' => 'edit')) ?>?id={{item.goods_id}}"><i
                                        class="glyphicon glyphicon-edit"></i> Редактировать</a>
                        </li>
                        <li>
                            <a tabindex="-1" href="#" ng-click="delete(item.goods_id,item.cid)">
                                <i class="glyphicon glyphicon-trash"></i> Удалить
                            </a>
                        </li>
                    </ul>
                </div>
            </td>
        </tr>
    </table>

    <div>
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
            <span ng-bind-html="to_trusted(html)"></span>
            <button ng-click="editSelectGoods()" type="button" class="btn btn-default">Готово</button>
        </form>
    </div>
    <?= $pagination->render(); ?>

</div>

<style type="text/css">

    .popular {
        font-size: 12px;
        padding-left: 10px;
        color: #ccc;
    }

    .pos{
        width: 50px !important;
        text-align: center;
    }

</style>

<script type="text/javascript">
    window._goods_types = <?= $goods_types ? \Delorius\Utils\Json::encode((array)$goods_types) : '[]'?>;
    window._type_id = <?= $type_id?>;
    window._goods = <?= $goods ? \Delorius\Utils\Json::encode((array)$goods) : '{}' ?>;
    window._get = <?= $get ? \Delorius\Utils\Json::encode((array)$get) : '{}' ?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode((array)$images) : '[]' ?>;
    window._currency = <?= $currency ? \Delorius\Utils\Json::encode((array)$currency) : '[]'?>;

</script>