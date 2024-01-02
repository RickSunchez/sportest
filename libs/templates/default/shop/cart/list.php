<section class="b-page__show b-page__show_cart b-cart"
         data-ng-cloak
         data-ng-controller="CartController"
         data-ng-init="init()">

    <header class="b-page__header b-page__header_upper">
        <h1>Мои заказы</h1>
    </header>

    <table class="table table-bordered table-cart-goods">
        <tr>
            <th width="36">№</th>
            <th class="table-cart-goods__photo" width="122">Фото</th>
            <th>Наименование</th>
            <th class="table-cart-goods__price" width="135">Цена</th>
            <th width="100">Кол-во</th>
            <th class="table-cart-goods__summer" width="135">Сумма</th>
            <th width="75">Удалить</th>
        </tr>
        <tr data-ng-repeat="item in goods">
            <td class="i-center-td">{{$index+1}}</td>
            <td class="b-cart__image i-center-td">
                <img data-ng-if="item.image != '' " data-ng-src="{{item.image.preview}}" src="/source/images/no.png"
                     alt="">
                <img data-ng-if="item.image == '' " src="/source/images/no.png" alt="">
            </td>
            <td class="i-middle-td">
                <a class="b-cart__name" target="_blank" href="{{item.link}}">{{item.name}}</a>
            </td>
            <td class="i-middle-td b-cart__price">
                <div class="b-cart__price__value" data-ng-bind-html="item.price | to_html"></div>
            </td>
            <td class="i-middle-td">
                <div class="b-cart__counter b-table">
                    <a class="b-table-cell" title="Убавить" href="javascript:;" data-ng-click="minus(item)">-</a>
                    <input class="b-table-cell" input type="text"
                           data-ng-model="item.quantity"
                           data-ng-blur="change_amount(item,item.quantity)">
                    <a class="b-table-cell" title="Добавить" href="javascript:;" data-ng-click="plus(item)">+</a>
                </div>
            </td>
            <td class="i-middle-td b-cart__price">
                <div class="b-cart__price__value" data-ng-bind-html="item.price_all | to_html"></div>
            </td>
            <td class="i-center-td b-cart__delete">
                <i data-toggle="tooltip" data-placement="top" title="Удалить товар" class="glyphicon glyphicon-trash"
                   data-ng-click="delete(item)"></i>
            </td>
        </tr>
    </table>


    <table class="b-cart__btn-layout">
        <tr>
            <td><a data-ng-click="clear();" class="b-btn b-btn_clear" href="javascript:;">Очистить корзину</a></td>
            <td width="310">
                <span class="b-cart__name-little">Сумма заказа:</span>
                <span class="b-cart__total-price" data-ng-bind-html="basket.goods.price | to_html"></span>
            </td>
        </tr>
    </table>

    <section class="b-order-list">
        <header class="b-order-list__header">
            <h1>
                Способ оплаты
            </h1>
        </header>

        <div class="b-table b-payments-list">
            <div class="b-table-cell" data-ng-class="{active: paymentMethodId==1 }" >
                <div class="icon_delivery"
                     data-ng-class="{active: paymentMethodId==1 }"
                     data-ng-click="changePayment(1)"></div>
                <div class="name">
                    Наличными при получении
                </div>
                <div class="text">
                    Рассчитаться можно с курьером при получении заказа
                </div>
            </div>

            <div class="b-table-cell" data-ng-class="{active: paymentMethodId==2 }">
                <div class="icon_delivery"
                     data-ng-class="{active: paymentMethodId==2 }"
                     data-ng-click="changePayment(2)"></div>
                <div class="name">
                    Безналичный расчет
                </div>
                <div class="text">
                    Оплата по счету
                </div>
            </div>
        </div>


    </section>

    <section class="b-order-list">
        <header class="b-order-list__header">
            <h1>
                Способ доставки
            </h1>
        </header>

        <div class="b-table b-deliveries-list">
            <div class="b-table-cell" data-ng-class="{active: deliveryId==1 }">
                <div class="icon_delivery"
                     data-ng-class="{active: deliveryId==1 }"
                     data-ng-click="changeDelivery(1)"></div>
                <div class="name">
                    Самовывоз из офиса
                </div>
                <div class="text">
                   Товар можно получить у нас в офисе
                </div>
            </div>

            <div class="b-table-cell" data-ng-class="{active: deliveryId==2 }">
                <div class="icon_delivery"
                     data-ng-class="{active: deliveryId==2 }"
                     data-ng-click="changeDelivery(2)"></div>
                <div class="name">
                    Доставка до подъезда
                </div>
                <div class="text">
                    Наш курьер доставляет товар до указанного Вами места и передает со всеми необходимыми документами.
                </div>
            </div>
        </div>
    </section>

    <section class="b-order-list">
        <header class="b-order-list__header">
            <h1>
                Оформление заказа
            </h1>
        </header>

        <div class="b-table b-form-order-contact">
            <div class="b-table-row" data-ng-class="{'error-show':!isNameChanged && !isNameValid}">
                <div class="b-table-cell b-table-cell_label">Ваше Ф.И.О.:</div>
                <div class="b-table-cell b-table-cell_input">
                    <input type="text" data-ng-model="form.name" data-ng-change="isNameChanged = 1"/>

                    <p class="error">
                        Поле обязательно для заполнения
                    </p>
                </div>
            </div>
            <div class="b-table-row" data-ng-class="{'error-show':!isPhoneChanged && !isPhoneValid}">
                <div class="b-table-cell b-table-cell_label">Мобильный телефон:</div>
                <div class="b-table-cell  b-table-cell_input">
                    <input data-ng-model="form.phone" data-ng-change="isPhoneChanged = 1"
                           type="text"
                           class="js-phone-mask"
                           placeholder="+7 (___) ___-__-__"/>

                    <p class="error">
                        Поле обязательно для заполнения
                    </p>
                </div>
            </div>
            <div class="b-table-row" data-ng-class="{'error-show':!isEmailChanged && !isEmailValid}">
                <div class="b-table-cell b-table-cell_label">E-mail:</div>
                <div class="b-table-cell  b-table-cell_input">
                    <input data-ng-model="form.email" data-ng-change="isEmailChanged = 1" type="text"/>

                    <p class="error">
                        Поле обязательно для заполнения
                    </p>
                </div>
            </div>

            <div data-ng-if="deliveryId==2" class="b-table-row"
                 data-ng-class="{'error-show':!isAddressChanged && !isAddressValid}">
                <div class="b-table-cell b-table-cell_label">Адрес доставки:</div>
                <div class="b-table-cell  b-table-cell_input">
                    <input data-ng-model="form.address" data-ng-change="isAddressChanged = 1" type="text"/>

                    <p class="error">
                        Поле обязательно для заполнения
                    </p>
                </div>
            </div>

            <div class="b-table-row" data-ng-if="paymentMethodId==2">
                <div class="b-table-cell b-table-cell_label">Ваши реквизиты:</div>
                <div class="b-table-cell  b-table-cell_textarea">
                   <textarea data-ng-model="form.details"
                             placeholder="Юр.адрес, ИНН, КПП, Р/счет, К/счет, БИК"></textarea>
                </div>
            </div>

            <div class="b-table-row">
                <div class="b-table-cell b-table-cell_label">Ваш комментарий к заказу:</div>
                <div class="b-table-cell  b-table-cell_textarea">
                    <textarea data-ng-model="form.note"
                              placeholder="Ваши комментарии к заказу, например удобное время звонка менеджера"></textarea>
                </div>
            </div>
        </div>

    </section>

    <section class="b-line-footer">
        <div class="b-table">
            <div class="b-table-cell ">
                <a class="b-btn btn-ordering" href="javascript:;" data-ng-click="send();">оформить заказ</a>
            </div>
            <div class="b-table-cell">
                <span class="b-deliveries-result__label">Симость доставки: </span>
                <span class="b-deliveries-result__price" data-ng-bind-html="basket.delivery.price | to_html"></span>
            </div>
            <div class="b-table-cell b-cart__result">
                <span class="b-cart__name-little">Итого:</span>
                <span class="b-cart__total-price" data-ng-bind-html="basket.total.price | to_html"></span>
            </div>
        </div>

    </section>
</section>