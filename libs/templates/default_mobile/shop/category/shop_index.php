<article class="b-page__content">
    <? if (count($categories[0])): ?>
        <ul class="b-category__layout hListing">
            <? foreach ($categories[0] as $cat): ?>
                <li class="b-category__item b-table item">
                    <a href="<?= link_to_city('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>"
                       class="b-category__link b-table-cell url">
                        <span class="name"><?= $cat['name'] ?></span>
                    </a>
                </li>
            <? endforeach; ?>
        </ul>
    <? endif; ?>


</article>