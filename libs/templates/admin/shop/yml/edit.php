<div ng-controller="YmlEditCtrl" ng-init="init()">

    <h2>Настройка выгрузки Yandex Маркета</h2>


    <div class="form-horizontal well">


        <div class="form-group">
            <label class="col-sm-2 control-label">Файл:</label>

            <div class="col-sm-10" style="padding-top: 8px;">
                <span style="padding-right: 20px;">
                    /market/<input ng-model="yml.file" style="width: 100px;"/>.xml
                </span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Магазин</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="yml.name" class="form-control" placeholder="Название магазина"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="company">Компания</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="yml.company" class="form-control"
                       placeholder="Название компании"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="site">Сайт</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="yml.site" class="form-control" placeholder="http://"/>
            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-2 control-label"></div>
            <div class="col-sm-10">
                <label for="amount" class="form-control-static">
                    <input type="checkbox" ng-model="yml.amount" ng-true-value="'1'" id="amount"
                           ng-false-value="'0'"/> Не выгружать отсутствующий товар</label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2 control-label"></div>
            <div class="col-sm-10">
                <label for="delivery" class="form-control-static">
                    <input type="checkbox" ng-model="yml.delivery" ng-true-value="'1'" id="delivery"
                           ng-false-value="'0'"/> Доставка</label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2 control-label"></div>
            <div class="col-sm-10">
                <label for="pickup" class="form-control-static">
                    <input type="checkbox" ng-model="yml.pickup" ng-true-value="'1'" id="pickup"
                           ng-false-value="'0'"/> Самовывоз</label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2 control-label"></div>
            <div class="col-sm-10">
                <label for="store" class="form-control-static">
                    <input type="checkbox" ng-model="yml.store" ng-true-value="'1'" id="store"
                           ng-false-value="'0'"/> Покупка в торговом зале (шоурум)</label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2 control-label"></div>
            <div class="col-sm-10">
                <label class="form-control-static" for="params">
                    <input type="checkbox" ng-model="yml.params" ng-true-value="'1'" id="params"
                           ng-false-value="'0'"/> Показывать параметр товара</label>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-2 control-label"></div>
            <div class="col-sm-10">
                <label for="adult" class="form-control-static">
                    <input type="checkbox" ng-model="yml.adult" ng-true-value="'1'" id="adult"
                           ng-false-value="'0'"/> Для взрослых</label>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="sales_notes">sales_notes</label>

            <div class="col-sm-10">
                <textarea type="text" id="sales_notes" ng-model="yml.sales_notes" class="form-control"></textarea>
                <p class="form-control-static">
                    минимальную сумму заказа;
                    минимальное количество товара в заказе (кроме шин и дисков);
                    необходимость предоплаты;
                    варианты оплаты;
                    условия акции.
                </p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="utm">UTM_ метки</label>

            <div class="col-sm-10">
                <input type="text" id="utm" ng-model="yml.utm" class="form-control" />
                <p  class="help-block" >Пример: <b>?utm_source=market.yandex.ru&utm_term={id}</b></p>
            </div>
        </div>



        <div class="form-group">
            <label class="col-sm-2 control-label">Категории:</label>

            <div class="col-sm-10" style="padding-top: 8px;">

                <div class="btn-group" role="group" style="margin-bottom: 20px;">
                    <a ng-click="unSelectAll()" class="btn btn-danger btn-xs" href="javascript:;">Снять все</a>
                    <a ng-click="selectAll()" class="btn btn-primary btn-xs" href="javascript:;">Выбрать все</a>
                </div>


                <ul class="b-list b-list_main" ng-repeat="category in getCategories(0)" ng-include="'tpl_block'"
                    ng-init="init_view()"></ul>

            </div>
        </div>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>

    </div>


    <script type="text/ng-template" id="tpl_block">
        <li class="b-item b-item_{{category.cid}}" id="cat_{{category.cid}}"
            ng-class="{'b-item_child':has_child(category), 'b-item_selected':is_selected(category),'b-item_show':show_category(category)}">
            <span class="b-item_btn" ng-click="show_category_child(category)"></span>
            <input ng-model="category.check"
                   id="cat_chb_{{category.cid}}" value="{{category.cid}}" type="checkbox"
                   name="{{category.cid}}" ng-click="select_category(category)"/> {{category.name}}
            <ul class="b-list" ng-repeat="category in getCategories(category.cid)" ng-include="'tpl_block'"></ul>
        </li>
    </script>


</div>

<style type="text/css">
    .b-list {
        margin: 0;
        padding: 0;
        list-style: none;
        display: none;
    }

    .b-list_main {
        display: block;
    }

    .b-item_show > .b-list {
        display: block;
    }

    .b-item .b-item_btn {
        display: inline-block;
        height: 16px;
        width: 16px;
        text-align: center;
        font-size: 14px;
        line-height: 0.9;
        cursor: pointer;
    }

    .b-item.b-item_child > .b-item_btn {
        background-color: #fff;
        border: 1px solid #ccc;
    }

    .b-item.b-item_child > .b-item_btn:before {
        content: "+";
        display: inline-block;

    }

    .b-item.b-item_child.b-item_show > .b-item_btn:before {
        content: "-";
    }

    .b-item .b-item_btn:before {
        display: inline-block;
        content: "";
    }

    .b-list > .b-item .b-list {
        margin-left: 20px;;
    }

    .top-select input {
        margin-right: 10px;
    }

    .top-select label {
        font-size: 20px;
        font-weight: bold;
        text-transform: lowercase;
    }

    .top-select td:first-child {
        padding-left: 20px;
        width: 200px;
        vertical-align: middle;
    }
</style>

<script type="text/javascript">
    window._yml = <?= $yml ? \Delorius\Utils\Json::encode((array)$yml) : '{config:[]}' ?>;
</script>