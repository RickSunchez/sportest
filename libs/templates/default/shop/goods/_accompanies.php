<? if (count($goods)): ?>
    <section class="b-accompanies">
        <h2 class="b-accompanies__title">Также с этим товаром приобретают:</h2>

        <div class="b-products _grid ">
            <? foreach ($goods as $item): ?>
                <section data-id="<?= $item->pk(); ?>" id="product_<?= $item->pk(); ?>" class="b-product__item ">
                    <div class="b-product__link_image" title="<?= $item->name; ?>"
                         data-href="<?= $item->link(); ?>">
                        <? if ($item->image): ?>
                            <img class="photo lazy"
                                 data-original="<?= $item->image->preview ?>"
                                 src="/source/images/no.png"
                                 alt="" itemprop="image">
                        <? else: ?>
                            <img src="/source/images/no.png" alt="">
                        <? endif; ?>
                        <i></i>
                    </div>

                    <div class="b-product__info">
                        <h3>
                            <a class="b-product__link" href="<?= $item->link(); ?>">
                                <?= $item->name; ?>
                            </a>
                        </h3>
                    </div>

                    <div class="b-product__misc">
                        <div class="b-product__price">
                            <?= $item->getPrice(); ?>
                        </div>
                        <div class="b-product__btn">
                            <a class="b-btn <?= $basket->getQuantity($item->combination_hash) != 0 ? 'b-btn_inner' : '' ?>"
                               href="javascript:;" onclick="add_cart_item_acc(<?= $item->pk(); ?>,this);">
                                <?= $basket->getQuantity($item->combination_hash) != 0 ? 'Добавлен' : 'В корзину' ?>
                            </a>
                        </div>

                    </div>
                </section>

            <? endforeach; ?>

        </div>

    </section>
<? endif; ?>