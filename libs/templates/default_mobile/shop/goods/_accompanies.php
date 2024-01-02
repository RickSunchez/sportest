<? if (count($goods)): ?>
    <aside class="b-type">
        <h2 class="b-type__title">Также с этим товаром приобретают:</h2>

        <div class="b-type__list">
            <? foreach ($goods as $item): ?>

                <?= $this->partial('shop/goods/_item_empty', array('goods' => $item)) ?>

            <? endforeach ?>
        </div>

    </aside>
<? endif; ?>