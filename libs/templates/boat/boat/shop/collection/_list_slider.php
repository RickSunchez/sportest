<? if (count($collections)): ?>
    <aside class="b-collection-products">
        <div class="b-collection-products__layout">
            <? foreach ($collections as $item): ?>
                <div class="b-collection-product">
                    <a class="b-collection-product__link" title="<?= $this->escape($item['header']); ?>"
                       href="<?= link_to_city('shop_category_collection',
                           array('id' => $item['id'], 'url' => $item['url'])); ?>">
                        <?= $item['name'] ?>
                    </a>
                </div>
            <? endforeach; ?>
        </div>
    </aside>

    <script>
        $(function () {
            $('.b-collection-products__layout').slick({
                dots: false,
                speed: 300,
                infinite: false,
                slidesToScroll: 1,
                variableWidth: true
            });
        });
    </script>

<? endif; ?>


