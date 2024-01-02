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

        <a class="m-btn b-btn__further js-model--close" href="javascript:;" onclick="$.magnificPopup.close();">
            Продолжить покупку
        </a>

        <a class="m-btn b-btn__in-cart" href="<?= link_to('shop_cart'); ?>">Оформить заказ</a>


    </div>

</aside>