<footer class="m-footer">
    <a class="m-footer__btn-top" href="#top">Наверх</a>

    <? if ($phone = city_builder()->getAttr('phone')): ?>
        <div class="b-footer__contact">

            <div class="m-footer__city">
                Звонки по <?= city_builder()->getName4() ?>:

                <? if ($time_work = city_builder()->getAttr('time_work')): ?>
                    <br/>ПН-ПТ <?= $time_work ?>
                <? endif; ?>
                <? if ($time_work2 = city_builder()->getAttr('time_work2')): ?>
                    <br/>СБ: <?= $time_work2 ?>
                <? endif; ?>
                <? if ($time_work3 = city_builder()->getAttr('time_work3')): ?>
                    <br/>ВС: <?= $time_work3 ?>
                <? endif; ?>
            </div>
            <div class="m-footer__phone">
                <a href="tel:<?= $phone; ?>"><?= $phone; ?></a>
            </div>

            <? if ($wt = city_builder()->getAttr('wt')): ?>
                <? $wt_raw = city_builder()->getAttr('wt_raw') ?>
                <div class="m-footer__phone_wp ">
                    <a title="Написать в Whatsapp" target="_blank" href="<?= $wt_raw ?>">
                        <?= $wt ?>
                        <i class="fa fa-whatsapp"></i>
                    </a>
                </div>
            <? endif; ?>
        </div>
    <? endif; ?>


    <div class="b-footer__contact">
        <div class="m-footer__city">
            Бесплатный для других регионов России
        </div>
        <div class="m-footer__phone">
            <a href="tel:8 800 350–27–25">8 800 350–27–25</a>
        </div>
    </div>


    <div class="m-footer__info">
        Доставка по всей России и СНГ
    </div>

    <div class="m-copy">
        © 2007 - <?= date('Y') ?> Специализированный интернет-магазин Лодки, моторы, сервис, запчасти.
    </div>


    <div class="m-code">
        <? DI()->getService('header')->renderJs(); ?>
    </div>
</footer>


<aside data-model="callback" class="b-category-model">
    <div class="b-model__header">
        <button class="b-model__close js-model--close"></button>
        Обратный звонок
    </div>
    <div class="b-model__layout">
        <div class="b-popup__layout" data-ng-controller="CallbackController">
            <div class="b-popup__group">
                <div class="b-popup__label">Телефон<sup>*</sup></div>
                <input data-ng-model="form.phone" type="text" class="b-popup__input js-phone-mask"
                       placeholder="+7 (___) ___-__-__"/>
            </div>
            <div class="b-popup__group b-popup__group_btn">
                <a class="m-btn b-btn_callback" href="javascript:;"
                   data-ng-click="sendMobile('обратный звонок')">Отправить</a>
            </div>
            <div class="b-popup__group">
                <label class="b-popup__label-checkbox" for="form_check">
                    <input data-ng-true-value="1" data-ng-false-value="0" id="form_check" data-ng-model="check"
                           type="checkbox" class="b-popup__checkbox"/>
                    Я даю согласие на обработку моих персональных данных
                </label>
            </div>
        </div>
    </div>
</aside>


<aside data-model="order" class="b-category-model">
    <div class="b-model__header">
        <button class="b-model__close js-model--close"></button>
       Заказать
    </div>
    <div class="b-model__layout">
        <div class="b-popup__layout" data-ng-controller="CallbackController">
            <div class="b-popup__group">
                <div class="b-popup__label">Телефон<sup>*</sup></div>
                <input data-ng-model="form.phone" type="text" class="b-popup__input js-phone-mask"
                       placeholder="+7 (___) ___-__-__"/>
            </div>
            <div class="b-popup__group b-popup__group_btn">
                <a class="m-btn b-btn_callback" href="javascript:;"
                   data-ng-click="send_order_mobile('Заказать')">Отправить</a>
            </div>
            <div class="b-popup__group">
                <label class="b-popup__label-checkbox" for="form_check">
                    <input data-ng-true-value="1" data-ng-false-value="0" id="form_check" data-ng-model="check"
                           type="checkbox" class="b-popup__checkbox"/>
                    Я даю согласие на обработку моих персональных данных
                </label>
            </div>
        </div>
    </div>
</aside>

<aside data-model="credit_form" class="b-category-model">

    <div class="b-model__header">
        <button class="b-model__close js-model--close"></button>
        Оформить кредит
    </div>

    <div class="b-form-credit">
        <p>
            <img src="https://www.pochtabank.ru/images/pochtabank/logo.svg"/>
        </p>

        <button class="btn btn-outline-info btn-block" type="button"
                onclick="open_text('.b-form-credit__modal')">
            Требования для оформления кредита
        </button>


        <div class="b-form-credit__modal hide" tabindex="-1">

            <div class="modal-content">
                <div class="modal-header">
                    <h6 class="modal-title" id="exampleModalLongTitle"><strong>Требования к клиенту для
                            оформления кредита:</strong>
                    </h6>
                </div>
                <div class="modal-body">• Гражданство Российской Федерации
                    <br/>
                    • Постоянная регистрация в любом субъекте Российской Федерации
                    <br/>
                    • Наличие собственного мобильного, а также контактного и рабочего номера телефона
                    <br/>
                    • Возраст от 18 лет
                    <br/>
                    <br/>
                    <strong>Необходимые документы:</strong>
                    <br/>
                    • Паспорт гражданина РФ
                    <br/>
                    <strong>Срок действия кредитного решения:</strong>
                    <br/>
                    • 7 дней
                    <br/>
                    <br/>
                    <strong>Сумма заявки:</strong>
                    <br/>
                    • От 3 000 до 300 000 рублей
                    <p>
                        <small>Подписание документов кредитного договора возможно
                            <br/>
                            в <a href="https://www.pochtabank.ru/map">любом отделении Почта Банк</a></small>
                    </p>
                </div>
                <div class="modal-footer">
                    <button class="btn btn-outline-info" onclick="open_text('.b-form-credit__modal')" type="button">Закрыть
                    </button>
                </div>
            </div>

        </div>


        <div class="b-form-credit__line">
            <div class="b-form-credit__label">Укажите</div>
            <input class="b-form-credit__input" id="chekPrice"
                   placeholder="Стоимость товаров" title="От 3 до 300 тысяч рублей" type="number" required="">
            <input class="b-form-credit__input" id="firstPayment"
                   placeholder="Сумма первого взноса(без ПВ напишите 0)" required=""
                   title="(если без первого взноса напишите 0)" type="number">
        </div>

        <div class="b-form-credit__line">
            <div class="b-form-credit__label">Срок кредита</div>
            <select class="b-form-credit__input" id="termCredit">
                <option value="6">6 месяцев</option>
                <option value="8">8 месяцев</option>
                <option value="10">10 месяцев</option>
                <option value="12">12 месяцев</option>
                <option value="18">18 месяцев</option>
                <option selected="selected" value="24">2 года</option>
            </select>
        </div>

        <div class="b-form-credit__line">
            <div class="b-form-credit__label">Подать заявку:</div>
            <button class="btn btn-warning" onclick="credit_form2()" type="button">Заполню сам
            </button>
            <button class="btn btn-warning" data-dismiss="modal" onclick="credit_form1()" type="button">Нужен звонок
                от банка
            </button>
        </div>


    </div>


</aside>


<style>
    body {
        -webkit-animation-delay: 0.1s;
        -webkit-animation-name: fontfix;
        -webkit-animation-duration: 0.1s;
        -webkit-animation-iteration-count: 1;
        -webkit-animation-timing-function: linear;
    }

    @-webkit-keyframes fontfix {
        from {
            opacity: 1;
        }
        to {
            opacity: 1;
        }
    }
</style>