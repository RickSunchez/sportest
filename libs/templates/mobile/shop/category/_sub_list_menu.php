<? if (count($categories)): ?>
    <a class="b-link b-menu-horiz__link b-menu-horiz__link-select"
       href="javascript:;">
        <span class="name">Каталог</span>
    </a>
    <div class="b-menu-horiz__list">
        <? foreach ($categories as $cat): ?>
            <a class="b-link b-menu-horiz__link "
               href="<?= link_to_city('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>">
                <span class="name"><?= $cat['name'] ?></span>
            </a>
        <? endforeach; ?>
    </div>
<? endif; ?>