<section class="b-popup b-popup_cart js-model" data-ng-controller="OneClickController"
         data-ng-init='init(<?= \Delorius\Utils\Json::encode($product_data) ?>)'>
    <div class="b-popup__header">
        <button class="b-model__close" onclick="$.magnificPopup.close();"></button>
        Быстрый заказ
    </div>
    <div class="b-popup__layout">


        <div class="b-popup_cart__form">
            <div class="b-popup_cart__form__info">
                Менеджеры интернет магазина перезвонят Вам в ближайшее рабочее время.
                Режим работы интернет магазина в Екатеринбурге (MSK +2):<br/>
                <b>ПН-ПТ</b> 9:00-21:00<br/>
                <b>СБ, ВС</b> выходной
            </div>

            <div class="b-popup_cart__form__layout">

                <div class="b-popup_cart__form__group">
                    <div class="b-popup_cart__form__label">Мобильный телефон:</div>
                    <div class="b-popup_cart__form__input"><input class="js-phone-mask" type="text"
                                                                  data-ng-model="form.phone"
                                                                  placeholder="+7 (___) ___-__-__"/></div>
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

<script type="text/javascript">
    var __products = [];

    __products.push({
        "id": "<?= $goods->pk()?>",
        "name": '<?= $goods->name?>',
        "price": <?= $goods->getPrice(false, false)?>,
        "brand": '<?= $goods->getVendor(); ?>',
        "category": '<?= $goods->getCategoriesStr();?>'
    });

    $(function () {
        $(".js-phone-mask").mask("+7 (999) 999-99-99");

        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "add": {
                    "products": __products
                }
            }
        });
    });
</script>

