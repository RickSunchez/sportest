<section class="b-popup b-popup_cart js-model" data-ng-controller="OneClickController"
         data-ng-init='init(<?= \Delorius\Utils\Json::encode($product_data) ?>)'>
    <div class="b-popup__header">
        <button class="b-model__close" onclick="$.magnificPopup.close();"></button>
        Быстрый заказ
    </div>
    <div class="b-popup__layout">


        <div class="b-popup_cart__form">
            <div class="b-popup_cart__form__info">
                Пожалуйста, заполните контактную информацию.
            </div>

            <div class="b-popup_cart__form__layout">
                <div class="b-popup_cart__form__group">
                    <div class="b-popup_cart__form__label">Ваше имя:</div>
                    <div class="b-popup_cart__form__input"><input type="text" data-ng-model="form.name"/></div>
                </div>
                <div class="b-popup_cart__form__group">
                    <div class="b-popup_cart__form__label">Мобильный телефон:</div>
                    <div class="b-popup_cart__form__input"><input class="js-phone-mask" type="text"
                                                                  data-ng-model="form.phone"
                                                                  placeholder="+7 (___) ___-__-__"/></div>
                </div>
                <div class="b-popup_cart__form__group">
                    <div class="b-popup_cart__form__label">Электронная почта:</div>
                    <div class="b-popup_cart__form__input"><input type="text" data-ng-model="form.email"/></div>
                </div>
                <div class="b-popup_cart__form__group">
                    <div class="b-popup_cart__form__label">Комментарий:</div>
                    <div class="b-popup_cart__form__textarea"><textarea data-ng-model="form.note"></textarea></div>
                </div>
            </div>

            <a class="m-btn b-btn__in-cart" href="javascript:;" data-ng-click="send();">Отправить</a>

        </div>
        <!-- .b-popup_cart__form -->


    </div>

</section>

