<? if (count($goods)): ?>
    <section class="b-accompanies">
        <h2 class="b-accompanies__title">C этим товаром покупают:</h2>
        <? $rnd = \Delorius\Utils\Strings::random();?>
        <div class="b-products-line__products js-slick-<?=$rnd?>">

            <? foreach ($goods as $product): ?>

                <div class="b-products-line__product js-hover">
                    <div class="b-products-line__product-layout">

                        <? if ($per = $product->getPerDiscount()): ?>
                            <div class="b-products-line__disc" title="Скидка <?= $per ?>%">
                                - <?= $per; ?> %
                            </div>
                        <? endif; ?>

                        <a href="<?= $product->link(); ?>"
                           class="b-products-line__product-img"
                           title="<?= $this->escape($product->name); ?>">
                            <? if ($product->image): ?>
                                <img data-lazy="<?= $product->image->preview ?>"
                                     src="/source/images/no.png" alt="">
                            <? else: ?>
                                <img src="/source/images/no.png" alt="">
                            <? endif; ?>
                        </a>

                        <div class="b-products-line__product-name">
                            <a class="b-products-line__product-name-link"
                               title="<?= $this->escape($product->name); ?>"
                               href="<?= $product->link(); ?>">
                                <?= $product->name ?>
                            </a>
                        </div>
                        <div class="b-products-line__product-price">
                            <?= $product->getPrice(); ?>
                        </div>
                        <div class="b-products-line__product-btn">
                            <a class="b-btn <?= $basket->getQuantity($product->combination_hash) != 0 ? 'b-btn_inner' : '' ?>"
                               href="javascript:;" onclick="add_cart(<?= $product->pk(); ?>);">
                                <?= $basket->getQuantity($product->combination_hash) != 0 ? 'Добавлен' : 'В корзину' ?>
                            </a>
                        </div>

                    </div>
                </div>

            <? endforeach; ?>

        </div>
    </section>

    <script>
        $(function () {
            $('.js-slick-<?=$rnd?>').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 4,
                slidesToScroll: 1,
                arrows: true
            });
        });
    </script>
<? endif; ?>
