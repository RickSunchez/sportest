<? if (count($categories[0])): ?>

    <div class="b-menu-horiz__layout">

        <div class="b-menu-horiz__delivery">
            Каталог
        </div>
        <? foreach ($categories[0] as $cat): ?>

            <a class="b-link b-menu-horiz__link"
               href="<?= link_to_city('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>">
                <?= $cat['name'] ?>
            </a>

        <? endforeach ?>

    </div>

<? endif; ?>
