<div ng-controller="GoodsOptionCombinationEditCtrl" ng-init="init()">

    <div class="clearfix btn-group ">
        <a title="Назад к товару" class="btn btn-danger btn-xs"
           href="<?= link_to('admin_goods', array('action' => 'edit', 'id' => $goods['goods_id'])) ?>">
            <i class="glyphicon glyphicon-arrow-left"></i> Назад к товару
        </a>
        <a title="Список опций" class="btn btn-info btn-xs"
           href="<?= link_to('admin_option', array('action' => 'list', 'id' => $goods['goods_id'])) ?>">
            <i class="glyphicon glyphicon-th"></i> Список опций
        </a>
    </div>

    <h1>Комбинации опций к "<?= $goods['name'] ?>"</h1>
    <br/>


    <div class="btn-group">
        <a class="btn btn-default dropdown-toggle " data-toggle="dropdown" href="#">
            <i class="glyphicon glyphicon-cog"></i>
        </a>
        <ul class="dropdown-menu pull-left">
            <? if ($pagination->getItemCount() != 0): ?>
                <li>
                    <a href="javascript:;" ng-click="generation(true)">
                        <i class="glyphicon glyphicon-refresh"></i> Перестроить комбинации
                    </a>
                </li>
            <? endif ?>
            <li>
                <a href="javascript:;" ng-click="generation(false)">
                    <i class="glyphicon glyphicon-repeat"></i> Сгенерировать новые комбинации
                </a>
            </li>

            <li>
                <a href="<?= link_to('admin_option', array('action' => 'list', 'id' => $goods['goods_id'])) ?>">
                    <i class="glyphicon glyphicon-arrow-left"></i> Перейти к опциям
                </a>
            </li>
        </ul>


    </div>


    <div class="counter">
        <span class="step">Кол-во комбинации: <?= $pagination->getItemCount(); ?> /</span>

        <div class="btn-group">
            <a class="btn-text btn dropdown-toggle" data-toggle="dropdown">
                <?= $get['step'] ?>
                <span class="caret"></span> </a>
            <ul class="dropdown-menu">
                <? for ($i = 1; $i < 10; $i++): ?>
                    <li>
                        <a href="<?= link_to('admin_option', array('action' => 'combination', 'id' => $goods['goods_id'], 'step' => $i * ADMIN_PER_PAGE)); ?>">
                            <?= ($i * ADMIN_PER_PAGE) ?>
                        </a>
                    </li>
                <? endfor ?>
            </ul>
        </div>
    </div>

    <table class="table table-condensed table-bordered table-hover table-middle table-edit">
        <tr>
            <th class="i-center-td" width="55">Фото</th>
            <th>Комбинация</th>
            <th width="280">Данные товара</th>
            <th class="i-center-td" width="75"><i class="glyphicon glyphicon-sort-by-attributes-alt"></i></th>
            <th width="20"></th>
        </tr>
        <tr ng-repeat="item in combinations">
            <td>
                <label class="b-input-upload" for="comb_{{item.combination_hash}}">
                    <img ng-src="{{getImageSrc(item.combination_hash)}}" alt=""/>
                    <input id="comb_{{item.combination_hash}}" type="file" title="Загрузить фото"
                           ng-file-select="onFileSelect($files,item.combination_hash)"/>
                </label>
            </td>
            <td>
                <table class="table table-condensed table-combinations">
                    <tr ng-repeat="(o,v) in item.options">
                        <td width="50%">
                            {{options[o]}}:
                        </td>
                        <td>
                            {{variants[v]}}
                        </td>
                    </tr>
                </table>
            </td>
            <td>

                <div class="input-group">
                    <span class="input-group-addon edit" id="code_{{item.combination_hash}}">Артикул:</span>
                    <input ng-model="item.goods_article" ng-blur="change(item)"
                           type="text"
                           class="form-control"
                           id="code_{{item.combination_hash}}"
                           placeholder="Код товара">
                </div>

                <div class="input-group">
                    <span class="input-group-addon edit" id="code_{{item.combination_hash}}">Кол-во:</span>
                    <input ng-model="item.goods_amount" ng-blur="change(item)"
                           type="text"
                           class="form-control"
                           id="amount_{{item.combination_hash}}"
                           placeholder="Кол-во товара">
                </div>

            </td>
            <td class="i-center-td">
                <input ng-model="item.pos" class="pos" ng-blur="change(item)"/>
            </td>
            <td>
                <a class="btn btn-danger" href="javascript:;" ng-click="delete(item.combination_hash)">
                    <i class="glyphicon glyphicon-trash"></i>
                </a>
            </td>
        </tr>
    </table>

    <?= $pagination; ?>
</div>

<style>
    .table-combinations tr:first-child td {
        border-top: none;
    }

    .table-combinations td:first-child {
        text-align: right;
        padding-right: 20px;;

    }

    .b-combinations-goods-param label {
        padding-right: 10px;
        padding-bottom: 5px;
    }

</style>

<script type="text/javascript">
    window._goods = <?= $goods? \Delorius\Utils\Json::encode((array)$goods): '{}'?>;
    window._options = <?= $options? \Delorius\Utils\Json::encode((array)$options): '[]'?>;
    window._variants = <?= $variants? \Delorius\Utils\Json::encode((array)$variants): '[]'?>;
    window._combinations = <?= $combinations? \Delorius\Utils\Json::encode((array)$combinations): '[]'?>;
    window._images = <?= $images? \Delorius\Utils\Json::encode((array)$images): '[]'?>;
</script>