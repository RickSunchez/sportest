<? if (count($goods)): ?>
    <section class="b-like-products">
        <h2 class="b-like-product__title">Похожие товары</h2>

        <div class="b-like-product__list js-product-carousel">
            <? foreach ($goods as $item): ?>

                <?= $this->partial('shop/goods/_item_empty', array('goods' => $item)) ?>

            <? endforeach ?>
        </div>

    </section>
<? endif; ?>
