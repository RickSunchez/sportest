<div class="form-group">
    <a class="btn btn-sm btn-success" href="<?= link_to('admin_order', array('action' => 'list')); ?>">Назад</a>
</div>

<div ng-controller="OrderEditCtrl" ng-init="init()">

    <div class="form well form-horizontal">

        <div class="form-group">
            <label class="col-sm-3 control-label">Код заказа:</label>

            <div class="col-sm-9">
                <p class="form-control-static">{{order.number}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Способ оплаты:</label>

            <div class="col-sm-9">
                <p class="form-control-static">{{order.config.payment.name}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Статус:</label>

            <div class="col-sm-9">
                <div class="btn-group">
                    <button class="btn btn-default btn-xs dropdown-toggle" type="button" data-toggle="dropdown">
                        {{order.status_name}} <span class="caret"></span>
                    </button>
                    <ul class="dropdown-menu">
                        <li ng-repeat="s in status">
                            <a class="i-cursor-pointer form-control-feedback"
                               ng-click="re_status(s.id,order.order_id)">{{s.name}}</a>
                        </li>
                    </ul>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">Дата заказа:</label>

            <div class="col-sm-9">
                <p class="form-control-static">{{order.created}}</p>
            </div>
        </div>
        <div class="form-group" ng-if="order.date_edit">
            <label class="col-sm-3 control-label">Дата последнего изменения:</label>

            <div class="col-sm-9">
                <p class="form-control-static">{{order.updated}}</p>
            </div>
        </div>
        <div class="form-group">
            <label class="col-sm-3 control-label">E-mail:</label>

            <div class="col-sm-5">
                <input class="form-control" ng-model="order.email" ng-blur="editOrder(order)"/>
            </div>
        </div>
        <fieldset>
            <legend>Данные ползователя:</legend>
            <div class="form-horizontal">
                <div class="form-group col-sm-12" ng-repeat="opt in options">
                    <label class="col-sm-3 control-label">{{opt.name}}</label>

                    <div class="col-sm-9" ng-hide="showTextArea(opt)">
                        <input class="form-control" ng-model="opt.value" ng-blur="editOption(opt)"/>
                    </div>
                    <div class="col-sm-9" ng-show="showTextArea(opt)">
                            <textarea cols="30" ng-model="opt.value" class="form-control"
                                      ng-blur="editOption(opt)"></textarea>
                    </div>
                </div>
            </div>
        </fieldset>

    </div>
    <table class="table table-hover table-bordered table-edit">

        <tr>
            <th width="50">ID</th>
            <th>Артикул\Название</th>
            <th width="120">Кол-во</th>
            <th width="100" class="text-right">Цена (<?= SYSTEM_CURRENCY ?>)</th>
            <th width="30" class="i-center-td">#</th>
        </tr>

        <tr ng-repeat="item in items">
            <td>{{item.goods_id}}</td>
            <td><a target="_blank" href="<?= link_to('admin_goods', array('action' => 'edit')) ?>?id={{item.goods_id}}">{{item.config.goods.name}}</a><br/>{{item.article}}
            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon">{{item.config.goods.unit}}</span>
                    <input ng-blur="editItem(item)" type="text" class="form-control text-center"
                           ng-model="item.amount">
                </div>
            </td>
            <td>
                <input ng-blur="editItem(item)" class="form-control text-right" ng-model="item.value"/>
            </td>
            <td class="i-center-td">
                <i ng-click="deleteItem(item)"
                   title="Удалить" style="color: red;cursor: pointer;"
                   class="glyphicon glyphicon-remove"></i>
            </td>
        </tr>
        <tr class="active">
            <td colspan="3" class="text-right"><b>Стоимость товара: &nbsp;</b></td>
            <td class="text-right" colspan="2">
                <b>{{price(order.config.goods.value)}}</b>
            </td>

        </tr>
        <tr>
            <td colspan="2">
                <table>
                    <tr>
                        <td width="80"><b>Скидка:</b></td>
                        <td>
                            <input placeholder="Примечание" class="form-control"
                                   ng-model="order.config.discount.label"
                                   ng-blur="editDiscount(order.config.discount)"/>
                        </td>
                    </tr>
                </table>

            </td>
            <td>
                <div class="input-group">
                    <span class="input-group-addon">%</span>
                    <input type="text" class="form-control text-center" ng-model="order.config.discount.percent"
                           ng-blur="editDiscount(order.config.discount)">
                </div>
            </td>
            <td colspan="2" class="i-middle-td text-right"><b>{{price(order.config.discount.value)}}</b></td>

        </tr>
        <tr>
            <td colspan="3">
                <table>
                    <tr>
                        <td width="80"><b>Доставка:</b></td>
                        <td width="200">
                            <input placeholder="Способ" class="form-control"
                                   ng-model="order.config.delivery.name" ng-blur="editDelivery(order.config.delivery)"/>
                        </td>
                        <td style="padding-left: 10px;">
                            <input placeholder="Примечание" class="form-control"
                                   ng-model="order.config.delivery.desc" ng-blur="editDelivery(order.config.delivery)"/>
                        </td>
                    </tr>
                </table>
            </td>
            <td colspan="2">
                <input type="text" class="text-right form-control"
                       ng-model="order.config.delivery.value"
                       ng-blur="editDelivery(order.config.delivery)">
            </td>
        </tr>
        <tr class="active">
            <td colspan="3" class="text-right"><b>Итого: &nbsp;</b></td>
            <td class="text-right" colspan="2">
                <b>{{price(order.value)}}</b>
            </td>
        </tr>
    </table>


</div>
<script type="text/javascript">

    window._order = <?= $order ? \Delorius\Utils\Json::encode($order) : '[]' ;?>;
    window._status = <?= $status ? \Delorius\Utils\Json::encode($status) : '[]' ;?>;
    window._items = <?= $items ? \Delorius\Utils\Json::encode($items) : '[]' ;?>;
    window._options = <?= $options ? \Delorius\Utils\Json::encode($options) : '[]' ;?>;
    window._user = <?= $user ? \Delorius\Utils\Json::encode($user) : '{}' ;?>;
</script>
