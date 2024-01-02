<article class="b-page__show b-page__show_cart b-cart"
         data-ng-cloak
         data-ng-controller="CartController"
         data-ng-init="init()">

    <header class="b-page__header b-page__header_upper">
        <!--<h1>Моя корзина {{is_pay_online()}}</h1>-->
        <h1>Моя корзина</h1>
    </header>

    <table class="table table-bordered table-cart-goods">
        <tr>
            <th width="80">Фото</th>
            <th>Наименование</th>
            <th width="135">Цена</th>
            <th width="100">Количество</th>
            <th width="135">Сумма</th>
            <th width="75">Удалить</th>
        </tr>
        <tr data-ng-repeat="item in goods">
            <td class="b-cart__image i-center-td">
                <img data-ng-if="item.image != '' " data-ng-src="{{item.image.preview}}" src="/source/images/no.png"
                     alt="">
                <img data-ng-if="item.image == '' " src="/source/images/no.png" alt="">
            </td>
            <td class="i-middle-td">
                <a href="{{item.link}}">{{item.name}}</a>

                <div class="b-cart__values" data-ng-repeat="values in item.options">
                    <b>{{values.option}}:</b>
                    {{values.variant}}
                </div>
                <div class="b-cart__values" data-ng-if="item.is_amount == 0">
                   Под заказ
                </div>
            </td>
            <td class="i-middle-td b-cart__price">
                <div data-ng-if="item.value>0" class="b-cart__price__value"
                     data-ng-bind-html="item.price | to_html"></div>
            </td>
            <td class="i-middle-td">
                <div class="b-cart__counter">
                    <a title="Убавить" href="javascript:;" data-ng-click="minus(item)">-</a>
                    <input type="text"
                           data-ng-model="item.quantity"
                           data-ng-blur="change_amount(item,item.quantity)">
                    <a title="Добавить" href="javascript:;" data-ng-click="plus(item)">+</a>
                </div>
            </td>
            <td class="i-middle-td b-cart__price">
                <div class="b-cart__price__value" data-ng-bind-html="item.price_all | to_html"></div>
            </td>
            <td class="i-center-td">
                <i data-toggle="tooltip" data-placement="top" title="Удалить товар"
                   class="b-cart__btn-delete glyphicon glyphicon-trash"
                   data-ng-click="delete(item)"></i>
            </td>
        </tr>
    </table>

    <table class="b-cart__btn-layout">
        <tr>
            <td><a data-ng-click="clear();" class="b-btn b-btn_clean" href="javascript:;">Очистить корзину</a></td>
            <td width="310">
                <span class="b-cart__name-little">Сумма заказа:</span>
                <span class="b-cart__total-price" data-ng-bind-html="basket.goods.price | to_html"></span>
            </td>
        </tr>
    </table>

    <section class="b-order-list">
        <header class="b-order-list__header">
            Оформление заказа
        </header>


        <div class="b-order-list__label">
            Пожалуйста, заполните контактную информацию.<br/>
            Сотрудники службы заказа свяжутся с вами в рабочее время.
        </div>

        <div class="b-table ">

            <div class="b-table-cell l-cart-form">

                <div class="b-table b-form-order-contact">
                    <div class="b-table-row" data-ng-class="{'error-show':!isNameChanged && !isNameValid}">
                        <div class="b-table-cell b-table-cell_label">Ваше ФИО:</div>
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

                    <div data-ng-if="deliveryId!=1" class="b-table-row"
                         data-ng-class="{'error-show':!isAddressChanged && !isAddressValid}">
                        <div class="b-table-cell b-table-cell_label">Адрес доставки:</div>

                        <div class="b-table-cell b-table-cell_textarea b-table-cell_textarea_address">
                            <textarea data-ng-model="form.address" data-ng-change="isAddressChanged = 1"></textarea>

                            <p class="error">
                                Поле обязательно для заполнения
                            </p>
                        </div>
                    </div>

                    <div data-ng-if="paymentMethodId==4" class="b-table-row"
                         data-ng-class="{'error-show':!isCompanyChanged && !isCompanyValid}">
                        <div class="b-table-cell b-table-cell_label">Данные юр. лица:</div>

                        <div class="b-table-cell b-table-cell_textarea ">
                            <textarea placeholder="ИНН, КПП, ОГРН, ОКПО ..." data-ng-model="form.details"
                                      data-ng-change="isCompanyChanged = 1"></textarea>

                            <p class="error">
                                Поле обязательно для заполнения
                            </p>
                        </div>
                    </div>

                    <div class="b-table-row">
                        <div class="b-table-cell b-table-cell_label b-table-cell_label-textarea">
                            Комментарий к заказу:
                        </div>

                        <div class="b-table-cell b-table-cell_textarea">
                            <textarea data-ng-model="form.comment"></textarea>
                        </div>

                    </div>
                </div>

            </div>
            <div class="b-table-cell  l-cart-select">

                <header class="b-order-list__header">
                    Способ доставки
                </header>

                <div class="b-checkbox-list">
                    <div class="b-checkbox" data-ng-class="{active: deliveryId==1 }" data-ng-click="changeDelivery(1)">
                        <div class="b-checkbox__input">Cамовывоз</div>
                        <div class="b-checkbox__text">
                            г. Екатеринбург, Первомайская, 71 Б,
                            корпус 1, литер А
                        </div>
                    </div>
                    <div class="b-checkbox" data-ng-class="{active: deliveryId==2 }" data-ng-click="changeDelivery(2)">
                        <div class="b-checkbox__input">Доставка по г. Екатеринбургу</div>
                        <div class="b-checkbox__text">
                            Бесплатная доставка при сумме заказа от <?= PRICE_DELIVERY_FREE ?> р.
                        </div>

                    </div>
                    <div class="b-checkbox" data-ng-class="{active: deliveryId==3 }" data-ng-click="changeDelivery(3)">
                        <div class="b-checkbox__input">Транспортной компанией</div>
                        <div class="b-checkbox__text">
                            Доставка оплачивается отдельно при получении товара
                        </div>
                    </div>
                    <div class="b-checkbox" data-ng-class="{active: deliveryId==4 }" data-ng-click="changeDelivery(4)">
                        <div class="b-checkbox__input">Почтой России</div>
                        <div class="b-checkbox__text">
                            Доставка оплачивается отдельно при получении товара
                        </div>
                    </div>
                </div>


                <header class="b-order-list__header">
                    Способ оплаты
                </header>
                <div class="b-checkbox-list">
                    <div data-ng-show="is_pay_online()" class="b-checkbox" data-ng-class="{active: paymentMethodId==1 }"
                         data-ng-click="changePayment(1)">
                        <div class="b-checkbox__input">On-line оплата картой банка</div>
                        <div class="b-checkbox__text">
                            Оплата банковской картой на сайте (при оплате онлайн на сумму более 5000 руб., коммисия банка составляет 2.5%)
                        </div>
                    </div>
                    <div class="b-checkbox" data-ng-class="{active: paymentMethodId==2 }"
                         data-ng-click="changePayment(2)">
                        <div class="b-checkbox__input">Оплата в магазине</div>
                        <div class="b-checkbox__text">
                            Оплата наличными или картой банка в магазине
                        </div>
                    </div>
                    <div data-ng-show="is_cod()" class="b-checkbox" data-ng-class="{active: paymentMethodId==3 }"
                         data-ng-click="changePayment(3)">
                        <div class="b-checkbox__input">Наложный платеж</div>
                        <div class="b-checkbox__text">
                            Возможна для ТК СДЭК
                        </div>
                    </div>
                    <div class="b-checkbox" data-ng-class="{active: paymentMethodId==4 }"
                         data-ng-click="changePayment(4)">
                        <div class="b-checkbox__input">Оплата на расчетный счет</div>
                        <div class="b-checkbox__text">
                            Только для юридических лиц
                        </div>
                    </div>
                    <div class="b-checkbox" data-ng-class="{active: paymentMethodId==5 }"
                         data-ng-click="changePayment(5)">
                        <div class="b-checkbox__input">Кредит</div>
                        <div class="b-checkbox__text">
                            Кредит оформляется в Почта Банке
                        </div>
                    </div>

                </div>


            </div>
        </div>

    </section>


    <section class="b-cart__footer">
        <a class="b-btn b-btn_footer" href="javascript:;" data-ng-click="send();">Оформить заказ</a>



        <span class="b-cart__total-value">
            <span class="b-cart__name-little">Итого:</span>
            <span class="b-cart__total-price" data-ng-bind-html="basket.total.price | to_html"></span>
        </span>
    </section>

    <section class="b-cart__info">
        Нажимая кнопку «Оформить заказ», я принимаю условия "<a href="<?= snippet('page', 12) ?>">Пользовательского<br/>
            соглашения</a>" и даю своё согласие на
        обработку моих персональных данных
    </section>

</article>