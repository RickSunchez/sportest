<? if (count($goods)): ?>
<? $rnd = \Delorius\Utils\Strings::random();?>
    <div class="b-products-line__products js-slick-<?=$rnd?>">

        <? foreach ($items as $item): ?>
            <? if ($product = $goods[$item['product_id']]): ?>
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

                    </div>
                </div>


            <? endif; ?>
        <? endforeach; ?>

    </div>

    <script>
        $(function () {
            $('.js-slick-<?=$rnd?>').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 6,
                slidesToScroll: 1,
                arrows: true
            });
        });
    </script>
<? endif; ?>