<? if (count($categories)): ?>
    <ul class="b-category__layout b-category__layout_sub hListing">
        <? foreach ($categories as $cat): ?>
            <li class="b-category__item b-table item">
                <a href="<?= link_to_city('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>"
                   class="b-category__link b-table-cell url">
                    <span class="name"><?= $cat['name'] ?></span>
                </a>
            </li>
        <? endforeach; ?>
    </ul>
<? endif; ?>