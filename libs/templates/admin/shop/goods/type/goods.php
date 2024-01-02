<div ng-controller="GoodsTypeListCtrl" ng-init='init()'>
    <div class="clearfix btn-group ">
        <a title="Добвить товар" class="btn btn-success btn-xs open-popup" href="#goods_list">
            <i class="glyphicon glyphicon-plus"></i>
        </a>
    </div>
    <br/><br/>


    <table class="table table-condensed table-bordered table-hover table-middle">
        <tr>
            <th width="55">Фото</th>
            <th>Название</th>
            <th class="i-center-td" width="60"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i></th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in goods">
            <td>
                <img width="50" ng-src="{{getImageSrc(item.goods_id)}}" alt=""/>
            </td>
            <td>
                <div ng-if="item.article">Артикул: {{item.article}}</div>
                <a target="_blank" href="<?= link_to('admin_goods', array('action' => 'edit')) ?>?id={{item.goods_id}}">
                    <span ng-show="item.status == 1"> {{item.name}}</span>
                    <s ng-show="item.status == 0">{{item.name}}</s>
                </a>
            </td>
            <td class="i-center-td">
                <div class="pos " style="width:52px!important;margin: 0 auto;">
                    <i class="glyphicon glyphicon-chevron-up" ng-click="up(item,type_id)"></i>
                    <input value="{{getPos(item)}}" style="width: 20px; text-align: center;" disabled="disabled"/>
                    <i class="glyphicon glyphicon-chevron-down" ng-click="down(item,type_id)"></i>
                </div>
            </td>
            <td>
                <a class="btn btn-xs btn-danger" href="javascript:void(0);" ng-click="delete(item.goods_id,type_id)">
                    <i class="glyphicon glyphicon-trash"></i>
                </a>
            </td>
        </tr>
    </table>


    <div id="goods_list" class="b-popup mfp-hide ">
        <div class="title">Поиск товара</div>
        <form class="well">
            <div class="input-group">
                <input name="name" ng-model="name" class="form-control"/>
                <span title="Сохранить название" class="input-group-addon btn-success" ng-click="search()"
                      style="cursor: pointer;color: #ffffff;">
                         <i class="glyphicon glyphicon-search"></i>
                    </span>
            </div>
        </form>

        <table class="table table-condensed table-bordered table-hover table-middle">
            <tr>
                <th width="50">Фото</th>
                <th>Название</th>
                <th width="20"></th>
            </tr>
            <tr ng-repeat="item in goods_list">
                <td>
                    <img width="40" ng-src="{{getImageSrc(item.goods_id,1)}}" alt=""/>
                </td>
                <td>
                    <div ng-if="item.article">Артикул: {{item.article}}</div>
                    <a target="_blank"
                       href="<?= link_to('admin_goods', array('action' => 'edit')) ?>?id={{item.goods_id}}">
                        <span ng-show="item.status == 1"> {{item.name}}</span>
                        <s ng-show="item.status == 0">{{item.name}}</s>
                    </a>
                </td>
                <td>
                    <a class="btn btn-xs btn-success" href="javascript:void(0);" ng-click="add(item)">
                        <i class="glyphicon glyphicon-plus"></i>
                    </a>
                </td>
            </tr>
        </table>
    </div>


</div>

<script type="text/javascript">
    window._goods = <?= $goods ? \Delorius\Utils\Json::encode((array)$goods) : '[]' ?>;
    window._type_id = <?= $type_id ? $type_id : 'null' ?>;
    window._images = <?= $images ? \Delorius\Utils\Json::encode((array)$images) : '{}' ?>;
    window._types = <?= $types ? \Delorius\Utils\Json::encode((array)$types) : '[]' ?>;
</script>