<? if (count($categories)): ?>
    <section class="b-page__section b-page__section_categories">

        <ul class="b-cat-main__layout">
            <? foreach ($categories as $item): ?>
                <li data-href="<?= link_to_city('shop_category_list',
                    array('cid' => $item['id'], 'url' => $item['url'])); ?>"
                    class="b-cat-main__item b-cat-main__item_<?= $item['id'] ?>">
                    <div class="b-cat-main__image"></div>
                    <a class="b-cat-main__link" href="<?= link_to_city('shop_category_list',
                        array('cid' => $item['id'], 'url' => $item['url'])); ?>">
                        <?= $item['name'] ?>
                    </a>
                </li>
            <? endforeach; ?>
        </ul>

    </section>
<? endif; ?>