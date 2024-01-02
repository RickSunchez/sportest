<? if (count($goods)): ?>
    <aside class="b-type b-type_top">
        <h2 class="b-type__title  b-type__title_<?= $type_id ?>">Хиты продаж</h2>

        <div class="b-type__list">
            <? foreach ($goods as $item): ?>
                <?= $this->partial('shop/goods/_item_type', array('goods' => $item)) ?>
            <? endforeach ?>
        </div>


    </aside>
<? endif; ?>