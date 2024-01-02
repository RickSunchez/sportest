<? if (count($values)): ?>
    <aside class="sr-block sr-block_feature">
        <input id="sr-feature-<?= $filter->pk(); ?>" type="checkbox" class="sr-cb">
        <label for="sr-feature-<?= $filter->pk(); ?>" class="sr-label">
            <?= $filter->name ?>
        </label>

        <div class="sr-body">

            <? foreach ($values as $value): ?>
                <div class="b-filter-item__value b-filter-item__value_checkbox">
                    <input
                        class="b-filter-item__checkbox"
                        type="checkbox"
                        data-filter-id="<?= $filter->pk() ?>"
                        <?= $get['feature'][$feature->pk()][$value['value_id']] == $value['value_id'] ? 'checked' : ''; ?>
                        id="value_<?= $value['value_id']; ?>"
                        name="feature[<?= $feature->pk(); ?>][<?= $value['value_id']; ?>]"
                        value="<?= $value['value_id']; ?>"
                        />

                    <label class="b-filter-item__label i-value-<?= $value['value_id']; ?>"
                           for="value_<?= $value['value_id']; ?>">
                        <?= $value['name']; ?>
                        <? if (isset($units[$value['unit_id']])): ?>
                            <span class="b-filter-item__unit"><?= $units[$value['unit_id']]['abbr'] ?></span>
                        <? endif ?>
                    </label>
                </div>
            <? endforeach; ?>

        </div>
    </aside>
<? endif; ?>