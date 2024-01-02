<aside class="b-popup b-popup_cart">
    <h1 class="b-popup__title">Товар добавлен в корзину</h1>

    <div class="b-popup__layout">

        <div class="b-table b-popup_cart__item">
            <div class="b-table-cell b-popup_cart__image">
                <? if ($goods->image): ?>
                    <img alt="<?= $goods->image->name ?>" src="<?= $goods->image->preview ?>"/>
                <? else: ?>
                    <img alt="<?= $goods->name ?>" src="/source/images/no.png"/>
                <? endif; ?>
            </div>
            <div class="b-table-cell b-popup_cart__info">
                <h1><?= $goods->name ?></h1>
                <? if (count($goods->options)): ?>
                    <? foreach ($goods->options as $value): ?>
                        <div class="b-popup_cart__options">
                            <b><?= $value['option'] ?></b>: <?= $value['variant'] ?>
                        </div>
                    <? endforeach ?>
                <? endif; ?>
            </div>
            <div class="b-table-cell b-popup_cart__price">
                <span>цена:</span> <?= $goods->getPrice(); ?>
            </div>
        </div>


        <? if (count($additions)): ?>
            <section class="b-popup_cart__addition">
                <h2>Дополнения</h2>

                <? foreach ($additions as $addition): ?>
                    <div class="b-table b-popup_cart__item">
                        <div class="b-table-cell b-popup_cart__image">
                            <? if ($addition->image): ?>
                                <img alt="<?= $addition->image->name ?>" src="<?= $addition->image->preview ?>"/>
                            <? else: ?>
                                <img alt="<?= $addition->name ?>" src="/source/images/no.png"/>
                            <? endif; ?>
                        </div>
                        <div class="b-table-cell b-popup_cart__info">
                            <h1><?= $addition->name ?></h1>
                        </div>
                        <div class="b-table-cell b-popup_cart__price">
                            <span>цена:</span> <?= $addition->getPrice(); ?>
                        </div>
                    </div>
                <? endforeach; ?>
            </section>

        <? endif; ?>


        <div class="b-table b-popup_cart__btn">
            <div class="b-table-cell">
                <a class="b-btn b-btn__further" href="javascript:;" onclick="$.magnificPopup.close();">
                    Продолжить покупку
                </a>
            </div>
            <div class="b-table-cell">
                <a class="b-btn b-btn__in-cart" href="<?= link_to('shop_cart'); ?>">Оформить заказ</a>
            </div>
        </div>
    </div>

</aside>