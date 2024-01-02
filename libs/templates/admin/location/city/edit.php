<div ng-controller="CityController" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_city', array('action' => 'list')); ?>"
           class="btn btn-danger btn-xs">Назад</a>
    </div>
    <br/>

    <h1>Город</h1>

    <ul class="nav nav-tabs" id="myTab">
        <li class="active"><a href="#desc" data-toggle="tab">Описание</a></li>
        <li ng-show="city.id"><a href="#metro" data-toggle="tab">Метро</a></li>
        <li ng-show="city.id"><a href="#options" data-toggle="tab">Опции</a></li>
    </ul>

    <form class="form-horizontal well" role="form">
        <div class="tab-content">
            <div class="tab-pane active" id="desc">

                <div class="form-group">
                    <div class="col-sm-offset-2 col-sm-10">
                        <div class="checkbox">
                            <label>
                                <input type="checkbox" ng-model="city.status" ng-true-value="'1'" id="inputstatus"
                                       ng-false-value="'0'"/> Опубликовать
                            </label>
                        </div>
                    </div>
                </div>

                <div ng-show="city.id">
                    <div class="form-group">
                        <label class="col-sm-2 control-label" for="image">Загрузка</label>

                        <div class="col-sm-10">
                            <input type="file" ng-file-select="onFileSelect($files,city.id)">
                        </div>
                    </div>

                    <div class="form-group" ng-if="image.image_id">
                        <label class="col-sm-2 control-label" for="image">Фото</label>

                        <div class="col-sm-10">
                            <img ng-src="{{image.preview}}" alt="" width="100"/>
                            <a href="<?= link_to('admin_image', array('action' => 'edit')) ?>?id={{image.image_id}}"
                               title="Редактировать" class="btn btn-info btn-xs">
                                <i class="glyphicon glyphicon-pencil"></i>
                            </a>
                        </div>
                    </div>
                </div>

                <div class="form-group" ng-show="countries.length !=0">
                    <label class="col-sm-2 control-label" for="name">Страна</label>

                    <div class="col-sm-10">
                        <select ui-select2 ng-model="city.country_id" style="width: 100%">
                            <option value="0">Выберите страну</option>
                            <option value="{{c.id}}" ng-repeat="c in countries">{{c.name}}</option>
                        </select>
                    </div>
                </div>


                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name">Название</label>

                    <div class="col-sm-10">
                        <input type="text" id="name" ng-model="city.name" class="form-control"
                               placeholder="Название города"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="url">URL (чпу)</label>

                    <div class="col-sm-10">
                        <input type="text" id="url" ng-model="city.url" class="form-control"/>
                        <span class="help-block">Генерируется автоматически</span>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name_2">Название 2</label>

                    <div class="col-sm-10">
                        <input type="text" id="name_2" ng-model="city.name_2" class="form-control"
                               placeholder="В каком городе?"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name_3">Название 3</label>

                    <div class="col-sm-10">
                        <input type="text" id="name_3" ng-model="city.name_3" class="form-control"
                               placeholder="Из какого города?"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label" for="name_4">Название 4</label>

                    <div class="col-sm-10">
                        <input type="text" id="name_4" ng-model="city.name_4" class="form-control"
                               placeholder="По какому городу?"/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Префикс шаблона</label>

                    <div class="col-sm-10">
                        <input type="text" ng-model="city.prefix" class="form-control" placeholder=""/>
                    </div>
                </div>

                <div class="form-group">
                    <label class="col-sm-2 control-label">Кооринаты центра</label>

                    <div class="col-sm-3">
                        <input ng-model="options.location.l1" class="form-control" placeholder="l1"/>
                    </div>
                    <div class="col-sm-3">
                        <input ng-model="options.location.l2" class="form-control" placeholder="l2"/>
                    </div>
                    <div class="col-sm-3">
                        <input ng-model="options.location.zoom" class="form-control" placeholder="zoom"/>
                    </div>
                </div>

            </div>
            <!-- #deac -->

            <div class="tab-pane" id="metro">
                <p>Кол-во метро: {{metro.length}} </p>
                <table class="table table-condensed table-bordered table-hover">
                    <tr>
                        <th>Название</th>
                        <th>ЧПУ (url)</th>
                        <th width="200">Координаты</th>
                        <th class="i-center-td" width="60"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i>
                        </th>
                        <th width="35"></th>
                    </tr>
                    <tr>
                        <td>
                            <input ng-model="form.name" class="form-control"/>
                        </td>
                        <td>
                            <input ng-model="form.url" class="form-control" placeholder="Автоматически"/>
                        </td>
                        <td>
                            <input ng-model="form.locations" class="form-control"/>
                        </td>
                        <td>
                            <input ng-model="form.pos" class="form-control text-center"/>
                        </td>
                        <td class="i-middle-td">
                            <a href="javascript:;" ng-click="addMetro()" class="btn btn-success btn-xs">
                                <i class="glyphicon glyphicon-plus"></i>
                            </a>
                        </td>
                    </tr>
                    <tr ng-repeat="item in metro">
                        <td>
                            <input ng-model="item.name" class="form-control" ng-blur="saveMetro(item)"/>
                        </td>
                        <td>
                            <input ng-model="item.url" class="form-control" ng-blur="saveMetro(item)"/>
                        </td>
                        <td>
                            <input ng-model="item.locations" class="form-control" ng-blur="saveMetro(item)"/>
                        </td>
                        <td>
                            <input ng-model="item.pos" class="form-control" ng-blur="saveMetro(item)"/>
                        </td>
                        <td class="i-middle-td">
                            <a href="javascript:;" ng-click="deleteMetro(item)" class="btn btn-danger btn-xs">
                                <i class="glyphicon glyphicon-trash"></i>
                            </a>
                        </td>
                    </tr>
                </table>

            </div>
            <!-- #metro -->

            <div class="tab-pane" id="options">

                <div class="form-group" ng-repeat="item in options">
                    <label class="col-sm-2 control-label" for="name">{{getNameField(item.code)}}</label>

                    <div class="col-sm-10">
                        <input ng-model="item.value" class="form-control"/>
                    </div>
                </div>

            </div>
            <!-- #meta -->
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>

</div>

<script type="text/javascript">
    window._city = <?= $city? \Delorius\Utils\Json::encode((array)$city): '{status:"0",main:0,country_id:0}'?>;
    window._fields = <?= $fields? \Delorius\Utils\Json::encode((array)$fields): '{}'?>;
    window._image = <?= $image? \Delorius\Utils\Json::encode((array)$image): 'null'?>;
    window._options = <?= $options? \Delorius\Utils\Json::encode((array)$options): '[]'?>;
    window._countries = <?= $countries? \Delorius\Utils\Json::encode((array)$countries): '[]'?>;
    window._metro = <?= $metro? \Delorius\Utils\Json::encode((array)$metro): '[]'?>;
</script>




