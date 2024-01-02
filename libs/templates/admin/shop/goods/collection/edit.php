<div ng-controller="CollectionProductEditCtrl" ng-init="init()">


    <h2>Редактировать</h2>


    <form class="form-horizontal well" role="form">

        <div class="form-group">
            <label for="inputstatus" class="col-sm-2 control-label">Статус</label>

            <div class="col-sm-10">
                <p class="form-control-static">
                    <input type="checkbox" ng-model="collection.status" ng-true-value="'1'" id="inputstatus"
                           ng-false-value="'0'"/> Показать</p>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название группы</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="collection.name" class="form-control"
                       placeholder="Название группы"/>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Label</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="collection.label" class="form-control"
                       placeholder="Название в шаблоне"/>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="prefix">Шаблон</label>

            <div class="col-sm-10">
                <input type="text" id="prefix" ng-model="collection.prefix" class="form-control"/>
            </div>
        </div>

        <fieldset>
            <legend>Связи товаров
                <a title="Добвить товар" class="btn btn-success btn-xs popup-link-ajax"
                   href="<?= link_to('admin_goods_data', array('action' => 'goodsList')) ?>?type_id={{collection.type_id}}">
                    <i class="glyphicon glyphicon-plus"></i>
                </a>
            </legend>

            <!-- select -->
            <table class="table table-condensed table-bordered table-hover">
                <tr>
                    <th width="400">Товар</th>
                    <th class="i-center-td" width="55">Фото</th>
                    <th>Название</th>
                    <th class="i-center-td" width="50">
                        <i class="glyphicon glyphicon-sort-by-attributes-alt"></i>
                    </th>
                    <th width="20"></th>
                </tr>
                <tr ng-repeat="item in items">
                    <td style="vertical-align: middle;">{{getNameProduct(item)}} (id:{{item.product_id}})</td>
                    <td>
                        <label class="b-input-upload" for="image_{{item.id}}">
                            <img ng-src="{{getImageSrc(item.id)}}" alt=""/>
                            <input id="image_{{item.id}}" type="file" title="Загрузить фото"
                                   ng-file-select="onFileSelect($files,item.id)"/>
                        </label>
                    </td>
                    <td class="i-center-td">
                        <input name="name" ng-model="item.name" class="form-control"
                               ng-blur="edit(item)"/>
                    </td>
                    <td class="i-center-td">
                        <input name="pos" ng-model="item.pos"
                               class="form-control text-center " ng-blur="edit(item)"/>
                    </td>
                    <td class="i-center-td">
                        <a class="btn btn-danger" tabindex="-1" href="javascript:;" ng-click="delete(item.id);">
                            <i class="glyphicon glyphicon-trash"></i>
                        </a>
                    </td>
                </tr>
            </table>
            <!-- /select -->
        </fieldset>


        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>
    </form>


</div>

<script type="text/javascript">
    window._collection = <?= $collection ? \Delorius\Utils\Json::encode((array)$collection) : '{}'?>;
    window._items = <?= $items ? \Delorius\Utils\Json::encode((array)$items) : '[]'?>;
    window._goods = <?= $goods ? \Delorius\Utils\Json::encode((array)$goods) : '[]'?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode((array)$images) : '[]'?>;
</script>





