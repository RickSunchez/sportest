<div ng-controller="LineEditCtrl" ng-init="init()">
    <div>
        <a href="<?= link_to('admin_line_product', array('action' => 'list')); ?>"
           class="btn btn-danger btn-xs">Назад</a>
    </div>


    <h2>Редактирование выборки</h2>
    <br/>

    <form class="form-horizontal well" role="form">


        <div class="form-group">
            <label class="col-sm-2 control-label" for="name">Название</label>

            <div class="col-sm-10">
                <input type="text" id="name" ng-model="line.name" class="form-control"
                       placeholder="Название выборки"/>
            </div>
        </div>


        <div class="form-group">
            <label class="col-sm-2 control-label" for="pos">Приоритет</label>

            <div class="col-sm-10">
                <input type="text" id="pos" ng-model="line.pos" class="form-control" style="width: 50px;"
                       placeholder="0" parser-int/>
                <span class="help-block">Необязательное поле</span>
            </div>
        </div>

        <div class="form-group">
            <label class="col-sm-2 control-label" for="url">Ссылка</label>

            <div class="col-sm-5">
                <input type="text" id="url" ng-model="line.url" class="form-control" placeholder="Url"/>
            </div>
            <div class="col-sm-5">
                <input type="text" id="btn" ng-model="line.btn" class="form-control" placeholder="Название"/>
            </div>
        </div>

        <div class="form-group">
            <div class="col-sm-offset-2 col-sm-10">
                <button type="submit" ng-click="save()" class="btn btn-info">Сохранить</button>
            </div>
        </div>

        <div style="padding-bottom: 20px">
            <span style="font-size: 16px;margin-right: 10px;">Товары</span>
            <a style="margin-right: -10px" title="Добвить товар" class="btn btn-success btn-xs popup-link-ajax"
               href="<?= link_to('admin_goods_data', array('action' => 'goodsList')) ?>?type_id=1">
                <i class="glyphicon glyphicon-plus"></i>
            </a>
        </div>

        <table class="table table-condensed table-bordered table-hover table-middle">
            <tr>
                <th class="i-center-td" width="20">ID</th>
                <th width="75">Арт.</th>
                <th>Название</th>
                <th class="i-center-td" width="50">
                    <i title="Приоритет" class="glyphicon glyphicon-sort-by-attributes-alt"></i>
                </th>
                <th width="20"></th>
            </tr>
            <tr ng-repeat="item in items">
                <td>{{item.product_id}}</td>
                <td>{{getProduct(item.product_id).article}}</td>
                <td>{{getProduct(item.product_id).name}}</td>
                <td>
                    <input ng-model="item.pos" style="width: 20px; text-align: center;" ng-blur="saveItem(item)"
                           class="pos"/>
                </td>
                <td class="i-center-td">
                    <a title="Удалить" href="javascript:;" ng-click="delete(item.id)">
                        <i class="glyphicon glyphicon-trash"></i>
                    </a>
                </td>
            </tr>
        </table>



    </form>


</div>

<script type="text/javascript">
    window._line = <?= $line ? \Delorius\Utils\Json::encode((array)$line ): '{}'?>;
</script>






