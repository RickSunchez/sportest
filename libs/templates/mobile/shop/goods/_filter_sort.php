<aside data-model="sort" class="b-product-sort">
    <div class="b-model__header">
        <button class="b-model__close js-model--close"></button>
        Сортировать по
    </div>
    <div class="b-model__layout">


        <div>
            <? if ($url_current->isEqual($url_name_asc)): ?>
                <span class="b-sorting__item active">
                    <span>Популярности</span>
                    <img
                        src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9Ii04IDEyLjIgMjUuOSAyMC44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IC04IDEyLjIgMjUuOSAyMC44OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KCS5zdDB7ZmlsbDojM0M2RkVEO30NCjwvc3R5bGU+DQo8Zz4NCgk8Zz4NCgkJPHBvbHlnb24gY2xhc3M9InN0MCIgcG9pbnRzPSIxNy45LDE0LjQgMTUuOSwxMi4yIDAsMjkgLTUuOSwyNC4yIC04LDI2LjMgMC4yLDMzIDAuNiwzMi41IDAuNywzMi42IAkJIi8+DQoJPC9nPg0KPC9nPg0KPC9zdmc+DQo="
                        alt="">
                </span>
            <? else: ?>
                <a class="b-sorting__item " data-href="<?= $url_name_asc; ?>">по популярности</a>
            <? endif; ?>
        </div>

        <div>
            <? if ($url_current->isEqual($url_price_asc)): ?>
                <span class="b-sorting__item active">
                    <span>Цена: сначала дешевые</span>
                    <img
                        src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9Ii04IDEyLjIgMjUuOSAyMC44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IC04IDEyLjIgMjUuOSAyMC44OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KCS5zdDB7ZmlsbDojM0M2RkVEO30NCjwvc3R5bGU+DQo8Zz4NCgk8Zz4NCgkJPHBvbHlnb24gY2xhc3M9InN0MCIgcG9pbnRzPSIxNy45LDE0LjQgMTUuOSwxMi4yIDAsMjkgLTUuOSwyNC4yIC04LDI2LjMgMC4yLDMzIDAuNiwzMi41IDAuNywzMi42IAkJIi8+DQoJPC9nPg0KPC9nPg0KPC9zdmc+DQo="
                        alt="">
                </span>
            <? else: ?>
                <a class="b-sorting__item " data-href="<?= $url_price_asc; ?>">Цена: сначала дешевые</a>
            <? endif; ?>
        </div>

        <div>
            <? if ($url_current->isEqual($url_price_desc)): ?>
                <span class="b-sorting__item active">
                    <span>Цена: сначала дорогие</span>
                    <img
                        src="data:image/svg+xml;base64,PD94bWwgdmVyc2lvbj0iMS4wIiBlbmNvZGluZz0idXRmLTgiPz4NCjwhLS0gR2VuZXJhdG9yOiBBZG9iZSBJbGx1c3RyYXRvciAxOS4xLjAsIFNWRyBFeHBvcnQgUGx1Zy1JbiAuIFNWRyBWZXJzaW9uOiA2LjAwIEJ1aWxkIDApICAtLT4NCjxzdmcgdmVyc2lvbj0iMS4xIiBpZD0iTGF5ZXJfMSIgeG1sbnM9Imh0dHA6Ly93d3cudzMub3JnLzIwMDAvc3ZnIiB4bWxuczp4bGluaz0iaHR0cDovL3d3dy53My5vcmcvMTk5OS94bGluayIgeD0iMHB4IiB5PSIwcHgiDQoJIHZpZXdCb3g9Ii04IDEyLjIgMjUuOSAyMC44IiBzdHlsZT0iZW5hYmxlLWJhY2tncm91bmQ6bmV3IC04IDEyLjIgMjUuOSAyMC44OyIgeG1sOnNwYWNlPSJwcmVzZXJ2ZSI+DQo8c3R5bGUgdHlwZT0idGV4dC9jc3MiPg0KCS5zdDB7ZmlsbDojM0M2RkVEO30NCjwvc3R5bGU+DQo8Zz4NCgk8Zz4NCgkJPHBvbHlnb24gY2xhc3M9InN0MCIgcG9pbnRzPSIxNy45LDE0LjQgMTUuOSwxMi4yIDAsMjkgLTUuOSwyNC4yIC04LDI2LjMgMC4yLDMzIDAuNiwzMi41IDAuNywzMi42IAkJIi8+DQoJPC9nPg0KPC9nPg0KPC9zdmc+DQo="
                        alt="">
                </span>
            <? else: ?>
                <a class="b-sorting__item" data-href="<?= $url_price_desc; ?>">Цена: сначала дорогие</a>
            <? endif; ?>
        </div>
    </div>

</aside>

