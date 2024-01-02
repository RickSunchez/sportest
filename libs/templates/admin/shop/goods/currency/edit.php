<div ng-controller="CurrencyEditCtrl" ng-init="init()" >
    
<a href="<?= link_to('admin_currency',array('action'=>'list'));?>" class="btn btn-xs btn-info" >Назад</a>
<br/ ><br/ >
<form class="well form-horizontal" >
       

        <div>
            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputname">Название</label>

                <div class="col-sm-10">
                    <input type="text" ng-model="currency.name" id="inputname" class="form-control" />
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputcode">Код ISO</label>
                <div class="col-sm-10">
                    <input type="text" ng-model="currency.code" id="inputcode" class="form-control" placeholder="RUR" />
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputnominal">Номинал</label>
                <div class="col-sm-10">
                    <input type="text" ng-model="currency.nominal" id="inputnominal" class="form-control" />
                    <span class="help-block">Номинальное значения валюты</span>
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputvalue">По курсу рубля</label>
                <div class="col-sm-10">
                    <input type="text" ng-model="currency.value" id="inputvalue" class="form-control" />
                    <span class="help-block">стоимость номинального значения в рублях</span>
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputleft">Префикс</label>
                <div class="col-sm-10">
                    <input type="text" ng-model="currency.symbol_left" id="inputleft" class="form-control" />
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputright">Суфикс</label>
                <div class="col-sm-10">
                    <input type="text" ng-model="currency.symbol_right" id="inputright" class="form-control" />
                </div>
            </div>

            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputdecimal_place">Разрядность</label>
                <div class="col-sm-10">
                    <input parser-int placeholder="2" type="text" ng-model="currency.decimal_place" id="inputdecimal_place" class="form-control" />
                </div>
            </div>


            <div class="form-group required">
                <label class="col-sm-2 control-label" for="inputdecimal_type">Тип разрядности</label>
                <div class="col-sm-5">
                        <span class="nullable">
                            <select id="inputdecimal_type" ng-model="currency.decimal_type" class="form-control">
                                <option value="">--Выберите--</option>
                                <option ng-repeat="type in types" value="{{type.id}}">{{type.name}}</option>
                            </select>
                        </span>
                </div>
            </div>



            <div class="form-group">

                <div class="col-sm-offset-2 col-sm-10">
                    <button class="btn btn-primary" type="button" ng-click="save()" >Готово</button>
                </div>
            </div>
        </div>
</form>

</div>

<script type="text/javascript">
    window._currency =  <?= $currency ? \Delorius\Utils\Json::encode((array)$currency) : '{}' ;?>;
    window._types =  <?= $types ? \Delorius\Utils\Json::encode((array)$types) : '[]' ;?>;
</script>






