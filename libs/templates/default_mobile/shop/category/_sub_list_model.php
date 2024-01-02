<? if (count($categories)): ?>
    <aside data-model="categories" class="b-category-model">
        <div class="b-model__header">
            <button class="b-model__close js-model--close"></button>
            <?= product_cat($cid) ?>
        </div>
        <div class="b-model__layout">
            <ul class="b-category-model__list hListing">
                <? foreach ($categories as $cat): ?>
                    <li class="b-category-model__item item">
                        <a href="<?= link_to_city('shop_category_list', array('cid' => $cat['id'], 'url' => $cat['url'])) ?>"
                           class="b-category-model__link  url <?= isset($images[$cat['id']]) ? 'b-category-model__link-image' : '' ?>">
                            <? if (isset($images[$cat['id']])): ?>
                                <img class="b-category-model__image photo"
                                     src="<?= $images[$cat['id']]['preview'] ?>"
                                     alt="<?= $this->escape($cat['name']); ?>">
                            <? endif; ?>
                            <span class="name"><?= $cat['name'] ?></span>
                        </a>
                    </li>
                <? endforeach; ?>
            </ul>
        </div>
    </aside>
<? else: ?>

    <script type="text/javascript">
        $('.b-category-model__btn').remove();
    </script>

<? endif; ?>