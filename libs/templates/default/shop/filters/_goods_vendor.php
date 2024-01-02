<? if (count($vendors)): ?>
    <div class="b-filter-item b-filter-item_checkbox">
        <div class="b-filter-item__title"><?= $filter->name ?></div>
        <div class="b-filter-item__layout">
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
    </div>
<? endif; ?>