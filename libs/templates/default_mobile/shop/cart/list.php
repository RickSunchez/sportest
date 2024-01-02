<article class="b-page__content">
    <h1 class="b-page__title">Корзина</h1>

    <div class="b-cart"
         data-ng-cloak
         data-ng-controller="CartController"
         data-ng-init="init()">


        <div class="b-cart__item" data-ng-repeat="item in goods">

            <div class="b-table">
                <div class="b-table-cell l-cart__image">
                    <img data-ng-if="item.image != '' "
                         data-ng-src="{{item.image.preview}}" src="/source/images/no.png" alt="">
                    <img data-ng-if="item.image == '' " src="/source/images/no.png" alt="">
                </div>
                <div class="b-table-cell l-cart__link">
                    <div class="b-cart__name">{{item.name}}</div>
                    <div class="b-cart__values" data-ng-repeat="values in item.options">
                        <b>{{values.option}}:</b>
                        {{values.variant}}
                    </div>
                </div>
                <div class="b-table-cell l-cart__close">
                    <img src="/source/images/mobile/close.png" alt="" data-ng-click="delete(item)">
                </div>
            </div>

            <div class="b-cart__misc">
                <div class="b-table">
                    <div class="b-table-cell l-cart__counter">

                        <div class="b-cart__counter b-table">
                            <a class="b-table-cell" title="Убавить" href="javascript:;"
                               data-ng-click="minus(item)">-</a>

                            <div class="b-table-cell">
                                <input type="text"
                                       data-ng-model="item.quantity"
                                       data-ng-blur="change_amount(item,item.quantity)">
                            </div>

                            <a class="b-table-cell" title="Добавить" href="javascript:;"
                               data-ng-click="plus(item)">+</a>
                        </div>

                    </div>
                    <div class="b-table-cell l-cart__price" data-ng-bind-html="item.price | to_html">

                    </div>
                </div>
            </div>


        </div>

        <div class="b-page-show__text b-cart__total-layout" data-ng-if="basket.discount.is_active" >
            <span class="b-cart__name-little small">Сумма заказа: </span>
            <span class="b-cart__total-price small" data-ng-bind-html="getProductPrice() | to_html"></span>
            <br/>
            <span class="b-cart__discount small red" data-ng-if="basket.discount.is_active">{{basket.discount.label}}</span>
            <br/>
            <span class="b-cart__name-little">Итого:</span>
            <span class="b-cart__total-price" data-ng-bind-html="basket.discount.price | to_html"></span>
        </div>


        <div class="b-page-show__text b-cart__total-layout" data-ng-if="basket.discount.is_active == false" >
            <span class="b-cart__name-little">Сумма заказа:</span>
            <span class="b-cart__total-price" data-ng-bind-html="basket.discount.price | to_html"></span>
        </div>




        <section class="b-item-list b-order-form" id="form_user">
            <header class="b-item-list__header ">
                Оформление заказа
            </header>

            <div class="b-order-list__label">
                Пожалуйста, заполните контактную информацию.<br/>
                Сотрудники службы заказа свяжутся с вами в рабочее время.
            </div>

            <div class="b-form-order">
                <div class="b-form-order__row" data-ng-class="{'error-show':!isNameChanged && !isNameValid}">
                    <div class="b-form-order__cell cell_label">Фамилия Имя:</div>
                    <div class="b-form-order__cell cell_input">
                        <input type="text" data-ng-model="form.name" data-ng-change="isNameChanged = 1"/>

                        <p class="error">
                            Поле обязательно для заполнения
                        </p>
                    </div>
                </div>
                <div class="b-form-order__row" data-ng-class="{'error-show':!isPhoneChanged && !isPhoneValid}">
                    <div class="b-form-order__cell cell_label">Мобильный телефон:</div>
                    <div class="b-form-order__cell cell_input">
                        <input data-ng-model="form.phone" data-ng-change="isPhoneChanged = 1"
                               type="text"
                               class="js-phone-mask"
                               placeholder="+7 (___) ___-__-__"/>

                        <p class="error">
                            Поле обязательно для заполнения
                        </p>
                    </div>
                </div>
                <div class="b-form-order__row" data-ng-class="{'error-show':!isEmailChanged && !isEmailValid}">
                    <div class="b-form-order__cell cell_label">E-mail:</div>
                    <div class="b-form-order__cell cell_input">
                        <input data-ng-model="form.email" data-ng-change="isEmailChanged = 1" type="text"/>

                        <p class="error">
                            Поле обязательно для заполнения
                        </p>
                    </div>
                </div>

                <div class="b-form-order__row"
                     data-ng-class="{'error-show':!isAddressChanged && !isAddressValid}">
                    <div class="b-form-order__cell cell_label">Адрес доставки:</div>
                    <div class="b-form-order__cell  cell_input">
                        <input data-ng-model="form.address" data-ng-change="isAddressChanged = 1" type="text"/>

                        <p class="error">
                            Поле обязательно для заполнения
                        </p>
                    </div>
                </div>

                <div class="b-form-order__row">
                    <div class="b-form-order__cell cell_label">Ваш комментарий к заказу:</div>
                    <div class="b-form-order__cell  cell_textarea">
                    <textarea data-ng-model="form.note"
                              placeholder="Ваши комментарии к заказу, например удобное время звонка менеджера"></textarea>
                    </div>
                </div>

            </div>

        </section>


        <div class="b-cart__result b-page-show__text">

            <span class="b-cart__name-little">Итого:</span>
            <span class="b-cart__total-price" data-ng-bind-html="basket.total.price | to_html"></span>
        </div>

        <div class="b-cart__btn">
            <a class="b-btn btn-ordering" href="javascript:;" data-ng-click="send();">Оформить заказ</a>
        </div>

    </div>
</article>