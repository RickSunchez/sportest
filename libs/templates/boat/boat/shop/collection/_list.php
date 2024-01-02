<? if (count($collections)): ?>
    <aside class="b-subcategories">
        <div class="b-subcategories__layout">
            <? foreach ($collections as $item): ?>
                <div class="b-subcategories__item">
                    <a class="b-subcategories__image" href="<?= link_to_city('shop_category_collection',
                        array('id' => $item['id'], 'url' => $item['url'])); ?>">
                        <? if (isset($images[$item['id']])): ?>
                            <img data-lazy="<?= $images[$item['id']]['preview'] ?>"
                                 alt="<?= $this->escape($item['name']); ?>"
                                 src="/source/images/zero.gif"
                                 width="100" height="100">
                        <? else: ?>
                            <img src="/source/images/no.png" alt="" width="100" height="100">
                        <? endif; ?>
                    </a>
                    <a class="b-subcategories__link" href="<?= link_to_city('shop_category_collection',
                        array('id' => $item['id'], 'url' => $item['url'])); ?>">
                        <?= $item['name'] ?>
                    </a>
                </div>
            <? endforeach; ?>
        </div>
    </aside>

    <script>
        $(function () {


            $('.b-subcategories__layout').slick({
                lazyLoad: 'ondemand',
                slidesToShow: 7,
                slidesToScroll: 1,
                arrows: true,
            });

            $('.b-subcategories__layout').css('opacity','1').css('overflow','visible');

        })
    </script>

<? endif; ?>

<style>
    .b-subcategories__layout {
        opacity: 0;
        height: 145px;
        overflow: hidden;
    }
</style>


