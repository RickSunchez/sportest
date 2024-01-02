<div ng-controller="AttrUserCtrl" ng-init="init()">

   <h2>Редактирование атрибутов</h2>
    <div class="form-group well" >

        <div class="form-group">
            <input placeholder="Название группы атрибутов" type="text" class="form-control" ng-model="group.name" />
        </div>

        <div class="form-group">
            <button class="btn btn-success btn-xs" ng-click="add()" ><i class="glyphicon glyphicon-plus"></i> Добавить атрибут</button>
        </div>

        <div class="row">
            <label class="col-sm-8 control-label">Название</label>
            <label class="col-sm-2 control-label text-center">Code</label>
            <label class="col-sm-1 control-label text-center">Req<span style="color: #ff0000;">*</span></label>
            <label class="col-sm-1 control-label text-center">Позиция</label>
        </div>

        <div ng-repeat="item in attributes" ng-show="item.show">
            <div class="row">
                <div class="col-sm-8">
                    <div class="input-group" style="margin-bottom: 10px;">
                        <input placeholder="Название атрибута" ng-model="item.name" class="form-control"/>
                            <span title="Удалить" class="input-group-addon btn-danger" ng-click="delete(item.inc)"
                                  style="cursor: pointer;color: #ffffff;">
                                 <i class="glyphicon glyphicon-trash"></i>
                            </span>
                    </div>
                </div>
                <div class="col-sm-2">
                    <input type="text" ng-model="item.code" class="form-control" />
                </div>
                <div class="col-sm-1 text-center">
                    <input type="checkbox" ng-model="item.require" ng-true-value="1" ng-false-value="0"   title="Поле обязательное к заполнению" />
                </div>
                <div class="col-sm-1">
                    <input ng-model="item.pos" style="text-align: center;" ng-change="sort_attr()" class="form-control ng-pristine ng-valid">
                </div>
            </div>
        </div>

        <div class="form-group">
            <input type="button" class="btn btn-primary" value="Сохранить" ng-click="save()" />
        </div>
    </div>

</div>

<script type="text/javascript">
    window._group = <?= $group ? \Delorius\Utils\Json::encode($group) : ' {} ' ;?>;
    window._attributes = <?= $attributes_array ? \Delorius\Utils\Json::encode($attributes_array) : ' [] ' ;?>;
</script>