<aside class="b-product-sort">
    <div class="b-sorting">
        <span class="b-sorting__caption">Сортировать:</span>
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

    <div class="b-view-display">
        <span class="b-view-display__caption">Вид каталога:</span>
        <span title="Показать таблицей" onclick="display('grid');"
              class="b-view-display__item _grid <?= $_COOKIE['typeView'] != 'list' ? 'active' : '' ?>"><i
                class="glyphicon glyphicon-th"></i></span>
        <span title="Показать списком" onclick="display('list');"
              class="b-view-display__item _list <?= $_COOKIE['typeView'] == 'list' ? 'active' : '' ?> "><i
                class="glyphicon glyphicon-th-list"></i></span>
    </div>

</aside>