<section class="b-popup b-popup_cart">
    <h1 class="b-popup__title">Товар добавлен в корзину</h1>

    <div class="b-popup__layout">

        <div class="b-table b-popup_cart__item">
            <div class="b-table-cell b-popup_cart__image">
                <? if ($goods->image): ?>
                    <img alt="<?= $goods->image->name ?>" src="<?= $goods->image->preview ?>"/>
                <? else: ?>
                    <img src="/source/images/no.png" alt="">
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
                <div class="b-popup_cart__price">
                    <span>цена:</span> <?= $goods->getPrice(); ?>
                </div>
            </div>

        </div>

        <?if((float)$goods->amount == 0):?>
            <div class="b-popup_cart__alarm">Условия поставки товара, который в данный момент отсутствует в магазине нужно уточнять у менеджеров.
                Мы не берем на себя обязательств по цене и сроку поставки товара, который в данный момент не доступен
                у нас на складе или в магазине.</div>
        <?endif;?>

        <? if (count($additions)): ?>
            <section class="b-popup_cart__addition">
                <h2>Дополнения</h2>

                <? foreach ($additions as $addition): ?>
                    <div class="b-table b-popup_cart__item">
                        <div class="b-table-cell b-popup_cart__image">
                            <? if ($addition->image): ?>
                                <img alt="<?= $addition->image->name ?>" src="<?= $addition->image->preview ?>"/>
                            <? else: ?>
                                <img src="/source/images/no.png" alt="">
                            <? endif; ?>
                        </div>
                        <div class="b-table-cell b-popup_cart__info">
                            <h1><?= $addition->name ?></h1>

                            <div class="b-popup_cart__price">
                                <span>цена:</span> <?= $addition->getPrice(); ?>
                            </div>
                        </div>

                    </div>
                <? endforeach; ?>
            </section>

        <? endif; ?>


        <div class="b-popup_cart__btn">
            <a class="b-btn b-btn_further" href="javascript:;"
               onclick="$.magnificPopup.close();">
                Продолжить покупки
            </a>
            <a class="b-btn b-btn_in-cart" href="<?= link_to('shop_cart'); ?>">Оформить заказ</a>
        </div>
    </div>

</section>


<script type="text/javascript">

    var __products = [];

    __products.push({
        "id": "<?= $goods->pk()?>",
        "name": '<?= $goods->name?>',
        "price": <?= $goods->getPrice(false, false)?>,
        "brand": '<?=  $goods->getVendor();  ?>',
        "category": '<?= $goods->getCategoriesStr();?>',
        "quantity": <?= $quantity?>
    });

    <?foreach ($additions as $item):?>
    __products.push({
        "id": "<?= $item->pk()?>",
        "name": '<?= $item->name?>',
        "price": <?= $item->getPrice(false, false)?>,
        "brand": '<?= $item->getVendor(); ?>',
        "category": '<?= $item->getCategoriesStr();?>'
    });
    <?endforeach;?>

    $(function () {
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