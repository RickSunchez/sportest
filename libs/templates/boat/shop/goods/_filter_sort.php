<aside class="b-product-sort">
    <div class="b-sorting">

        <span class="b-sorting__caption">Сортировать:</span>

        <? if ($url_current->isEqual($url_name_asc)): ?>
            <span class="b-sorting__item active"><span>по популярности</span></span>
        <? else: ?>
            <a class="b-sorting__item " data-href="<?= $url_name_asc; ?>">по популярности</a>
        <? endif; ?>


        <? if ($url_current->isEqual($url_price_asc)): ?>
            <span class="b-sorting__item active"><span>сначала дешевые</span></span>
        <? else: ?>
            <a class="b-sorting__item " data-href="<?= $url_price_asc; ?>">сначала дешевые</a>
        <? endif; ?>

        <? if ($url_current->isEqual($url_price_desc)): ?>
            <span class="b-sorting__item active"><span>сначала дорогие</span></span>
        <? else: ?>
            <a class="b-sorting__item" data-href="<?= $url_price_desc; ?>">сначала дорогие</a>
        <? endif; ?>
    </div>

</aside>