<? if (count($goods)): ?>
    <div class="b-popular__products">

        <? foreach ($items as $item): ?>
            <? if ($product = $goods[$item['product_id']]): ?>
                <div class="b-popular__product js-hover">
                    <div class="b-popular__product-layout">
                        <a href="<?= $product->link(); ?>"
                           class="b-popular__product-img"
                           title="<?= $this->escape($product->name); ?>">
                            <? if ($product->image): ?>
                                <img class="photo lazy"
                                     data-original="<?= $product->image->preview ?>"
                                     src="/source/images/no.png" alt="">
                            <? else: ?>
                                <img src="/source/images/no.png" alt="">
                            <? endif; ?>
                        </a>

                        <div class="b-popular__product-name">
                            <a class="b-popular__product-name-link"
                               title="<?= $this->escape($product->name); ?>"
                               href="<?= $product->link(); ?>">
                                <?= $product->name ?>
                            </a>
                        </div>
                        <div class="b-popular__product-price">
                            <?= $product->getPrice(); ?>
                        </div>

                    </div>
                </div>


            <? endif; ?>
        <? endforeach; ?>

    </div>
<? endif; ?>