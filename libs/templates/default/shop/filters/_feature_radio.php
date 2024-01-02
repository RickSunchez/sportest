<? if (count($values)): ?>
    <div class="b-filter-item b-filter-item_radio">
        <div class="b-filter-item__title"><?= $filter->name ?></div>
        <div class="b-filter-item__layout">
        <? foreach ($values as $value): ?>
           <div class="b-filter-item__value b-filter-item__value_checkbox">
                    <input
                        class="b-filter-item__checkbox"
                        type="radio"
                        <?= $get['feature'][$feature->pk()][$value['value_id']] == $value['value_id'] ? 'checked' : ''; ?>
                        id="value_<?= $value['value_id']; ?>"
                        name="feature[<?= $feature->pk(); ?>]"
                        value="<?= $value['value_id']; ?>"
                        />

                    <label class="b-filter-item__label i-value-<?= $value['value_id']; ?>"
                           for="value_<?= $value['value_id']; ?>">
                        <?= $value['name']; ?>
                        <? if (isset($units[$value['unit_id']])): ?>
                            <span class="b-filter-item__unit"><?= $units[$value['unit_id']]->abbr ?></span>
                        <? endif ?>
                    </label>
                </div>
        <? endforeach; ?>
        </div>
        </div>
    </div>
<? endif; ?>