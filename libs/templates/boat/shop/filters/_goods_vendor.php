<? if (count($vendors)): ?>
    <aside class="sr-block sr-block_feature">
        <input id="sr-feature-<?= $filter->pk(); ?>" type="checkbox" class="sr-cb">
        <label for="sr-feature-<?= $filter->pk(); ?>" class="sr-label">
            <?= $filter->name ?>
        </label>

        <div class="sr-body">
            <? foreach ($vendors as $vendor): ?>
                <div class="b-filter-item__value b-filter-item__value_checkbox">
                    <input
                        <?= $get['vendors'][$vendor['vendor_id']] == $vendor['vendor_id'] ? 'checked' : ''; ?>
                        class="b-filter-item__checkbox"
                        type="checkbox"
                        id="vendor_<?= $vendor['vendor_id']; ?>"
                        name="vendors[<?= $vendor['vendor_id']; ?>]"
                        value="<?= $vendor['vendor_id'] ?>"/>

                    <label class="b-filter-item__label i-vendor-<?= $vendor['vendor_id']; ?>"
                           for="vendor_<?= $vendor['vendor_id']; ?>">
                        <?= $vendor['name']; ?> <span class="b-filter-item__count">(<?= $vendor['count']; ?>)</span>
                    </label>
                </div>
            <? endforeach; ?>
        </div>
    </aside>
<? endif; ?>

