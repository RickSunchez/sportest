<? if (count($ids)): ?>
    <section class="b-like-products">
        <h2 class="b-like-product__title">Вы смотрели</h2>

        <div class="b-like-product__list js-product-carousel">
            <? foreach ($ids as $id): ?>
                <? if (isset($goods[$id])): ?>
                    <?= $this->partial('shop/goods/_item_empty', array('goods' => $goods[$id])) ?>
                <? endif; ?>
            <? endforeach ?>
        </div>

    </section>

<? endif; ?>

