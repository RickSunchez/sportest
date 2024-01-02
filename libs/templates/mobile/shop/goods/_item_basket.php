<aside class="b-popup js-model">
    <div class="b-popup__header">
        Товар добавлен в корзину
    </div>

    <div class="b-popup__layout">

        <div class="b-popup_cart__item">
            <div class="b-popup_cart__info">
                <h2 class="name"><?= $goods->name ?></h2>
            </div>
            <? if ($goods->image): ?>
                <div class=" b-popup_cart__image">
                    <img alt="" src="<?= $goods->image->preview ?>"/>
                </div>
            <? endif; ?>
            <div class="b-popup_cart__price"><span>Цена:</span> <?= $goods->getPrice(); ?></div>
        </div>

        <?if($goods->amount):?>
            <div class="b-popup_cart__alarm">Условия поставки товара, который в данный момент отсутствует в магазине нужно уточнять у менеджеров.
                Мы не берем на себя обязательств по цене и сроку поставки товара, который в данный момент не доступен
                у нас на складе или в магазине.</div>
        <?endif;?>


        <a class="m-btn b-btn__further js-model--close" href="javascript:;" onclick="$.magnificPopup.close();">
            Продолжить покупки
        </a>

        <a class="m-btn b-btn__in-cart" href="<?= link_to('shop_cart'); ?>">Оформить заказ</a>


    </div>

</aside>

<script type="text/javascript">

    var __product = {
        "id": "<?= $goods->pk()?>",
        "name": '<?= $goods->name?>',
        "price": <?= $goods->getPrice(false, false)?>,
        "brand": '<?=  $goods->getVendor();  ?>',
        "category": '<?= $goods->getCategoriesStr();?>',
        "quantity": <?= $quantity?>
    };

    $(function () {
        dataLayer.push({
            "ecommerce": {
                "currencyCode": "RUB",
                "add": {
                    "products": [__product]
                }
            }
        });
    });
</script>