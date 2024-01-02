<div ng-controller="GoodsPopupCtrl" class="b-popup b-popup_goods" ng-init="init(<?= (int)$type_id ?>,<?= (int)$cid?>,'<?=$select?>','<?= $option?>')">
    <div class="b-popup__title">Поиск сопутствующего товара</div>

    <table class="table table-condensed table-bordered table-hover table-middle">
        <tr class="active">
            <td><input name="name" ng-model="form.name" placeholder="Название товара, ID " class="form-control"/></td>
            <td class="i-center-td" width="20">
                <button title="Поиск" ng-click="search()" type="button" class="btn btn-info btn-xs"><i
                        class="glyphicon glyphicon-search"></i></button>
            </td>
        </tr>
        <tr class="active">
            <td colspan="2" style="text-align: right;">
                <a ng-click="form_adv=1" href="javascript:;">Расширеный поиск</a>
            </td>
        </tr>
        <tr class="active" ng-show="form_adv == 1">
            <td colspan="2">

                <div class="form-group">
                    <label for="inputarticle">Артикул товара</label>
                    <input ng-model="form.article" class="form-control" id="inputarticle" placeholder="">
                </div>
                <div class="form-group">
                    <label for="inputstatus">Категория:
                        <img style="width: 20px" src="/source/images/load.svg" alt="" class="category_load "></label>
                    <select ui-select2 ng-model="form.cid" style="width: 100%">
                        <option value="{{cat.value}}" ng-selected="cat.selected" ng-disabled="cat.disabled"
                                ng-repeat="cat in categories">{{cat.seporator}} {{cat.name}}
                        </option>
                    </select>
                </div>

            </td>
        </tr>
    </table>


    <? if (count($types) >= 2): ?>
        <select ng-model="typeId" ng-change="selectType(typeId);">
            <? foreach ($types as $type): ?>
                <option value="<?= $type['id'] ?>"><?= $type['name'] ?></option>
            <? endforeach ?>
        </select>
        <br/>
    <? endif; ?>

    <div class="goods">


        <table class="table table-condensed table-bordered table-hover table-middle">
            <tr>
                <th class="i-center-td" width="30">ID</th>
                <th width="50">Арт.</th>
                <th>Название</th>
                <th class="i-center-td" width="50">#</th>

            </tr>
            <tr ng-repeat="goods in getGoods()">
                <td class="i-center-td">{{goods.goods_id}}</td>
                <td class="i-middle-td">{{goods.article}}</td>
                <td>{{goods.name}}</td>
                <td class="i-center-td" style="width: 30px">
                    <button ng-click="addGoods(goods)" title="Добавить" type="button"
                            class="btn btn-success btn-xs">
                        <i class="glyphicon glyphicon-plus"></i>
                    </button>
                </td>
            </tr>
        </table>

        <div ng-show="btn==1" style="text-align: center;padding: 10px">
            <a class="btn btn-success" href="javascript:;" ng-click="next()">Показать еще</a>
        </div>

    </div>

</div>


