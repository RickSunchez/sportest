<? if (count($categories)): ?>
    <div class="b-filter-item b-filter-item_categories">
        <div class="b-filter-item__title"><?= $filter->name ?></div>
        <? foreach ($categories as $cat): ?>
            <div class="b-filter-item__value b-filter-item__value_cat">
                <? if ($cat->children): ?>
                    <a class="b-link b-filter-item__link" href="<?= link_to('shop_category_list', array('cid' => $cat->pk(), 'url' => $cat->url)) ?>"><?= $cat->name; ?></a>
                <? else: ?>
                    <label class="b-filter-item__label i-cat-<?= $cat->pk(); ?>" for="cat_<?= $cat->pk(); ?>">
                        <input
                            class="b-filter-item__checkbox"
                            id="cat_<?= $cat->pk(); ?>"
                            type="checkbox"
                            name="cats[<?= $cat->pk() ?>]"
                            value="<?= $cat->pk() ?>"
                            />
                        <?= $cat->name; ?>
                    </label>
                <? endif; ?>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>

