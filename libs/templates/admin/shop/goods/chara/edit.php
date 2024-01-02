<div ng-controller="CharaEditCtrl" ng-init='init()'>

    <h1>Характеристика</h1>

    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="chara.name" class="form-control"
                       placeholder="Название характеристики"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="info">Описание</label>

            <div class="col-sm-10">
                <textarea type="text" id="info" ng-model="chara.info" class="form-control"
                       placeholder="Описание характеристики" ></textarea>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="pos">Приоритет</label>

            <div class="col-sm-10">
                <input type="text" id="pos" ng-model="chara.pos" class="form-control" style="width: 50px;"
                       placeholder="0" parser-int/>
                <span class="help-block">Не обязательное поле</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="inputfilters">Фильтр</label>

            <div class="col-sm-3">
                <select id="inputfilters" ng-model="chara.filter" class="form-control select_value"
                        data-value="{{chara.filter}}">
                    <option ng-repeat="f in filters" value="{{f.id}}">{{f.name}}</option>
                </select>
            </div>
        </div>

        <div class="form-group" ng-show="chara.filter == <?= \Shop\Commodity\Entity\Characteristics::FILTER_OTHER ?>">
            <label class="col-sm-2 control-label" for="filter_other">Другой шаблон</label>

            <div class="col-sm-10">
                <input type="text" id="filter_other" ng-model="chara.filter_other" class="form-control"
                       placeholder="Укажите название шаблона фильтра _feature_**** "/>
            </div>
        </div>

        <div ng-show="value.show" id="color_inc_{{value.inc}}" class="form-group" ng-repeat="value in values">

            <table class="table table-condensed table-hover table-middle">
                <tr>
                    <td>Значение [{{value.value_id}}]</td>
                    <td>Ед. измереи</td>
                    <td>Приоритет</td>
                    <td></td>
                </tr>
                <tr>
                    <td >
                        <input type="text" id="name_{{value.inc}}" ng-model="value.name" class="form-control"
                               placeholder="Значение"/>
                    </td>
                    <td width="200">
                        <select ng-model="value.unit_id" class="form-control select_value"
                                data-value="{{value.unit_id}}">
                            <option value="0">Без измерений</option>
                            <option ng-repeat="u in units" value="{{u.unit_id}}">{{u.abbr}} ({{u.name}})</option>
                        </select>
                    </td>
                    <td width="80">
                        <input parser-int type="text" id="pos_{{value.inc}}" ng-model="value.pos" class="form-control"
                               placeholder="0"/>
                    </td>
                    <td width="20">
                        <a ng-click="deleteValue(value.inc)" class="btn btn-danger" href="javascript:void(0)"><i
                                class="glyphicon glyphicon-trash"></i></a>
                    </td>
                </tr>
                <tr>
                    <td>
                        <textarea id="info_{{value.inc}}"
                                  ng-model="value.info"
                                  class="form-control"
                                  placeholder="Примечание"></textarea>
                    </td>
                    <td colspan="3">
                        <div>Код значения:</div>
                        <input type="text" id="code_{{value.inc}}" ng-model="value.code" class="form-control"
                               placeholder="Код"/>
                    </td>
                </tr>
            </table>
            

        </div>


        <div class="form-group">
            <div class="col-sm-10 col-sm-offset-2">
                <a href="javascript:void(0);" ng-click="addValue()" class="btn btn-success btn-xs">
                    <i class="glyphicon glyphicon-plus"></i>
                    Добавить значение
                </a>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._chara = <?= $chara? \Delorius\Utils\Json::encode((array)$chara): '{filter:1}' ?>;
    window._values = <?= $values? \Delorius\Utils\Json::encode((array)$values): '[]' ?>;
    window._units = <?= $units? \Delorius\Utils\Json::encode((array)$units): '[]' ?>;
    window._filters = <?= $filters? \Delorius\Utils\Json::encode((array)$filters): '[]' ?>;
</script>


