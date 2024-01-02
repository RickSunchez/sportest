<? if (count($values)): ?>
    <div class="b-filter-item b-filter-item_checkbox">
        <div class="b-filter-item__title"><?= $filter->name ?></div>
        <div class="b-filter-item__layout">
            <? foreach ($values as $value): ?>
                <div class="b-filter-item__value b-filter-item__value_checkbox">
                    <label class="b-filter-item__label i-value-<?= $value->pk(); ?>" for="value_<?= $value->pk(); ?>">
                        <input
                            class="b-filter-item__checkbox"
                            type="checkbox"
                            <?= $get['feature'][$feature->pk()][$value->pk()] == $value->pk() ? 'checked' : ''; ?>
                            id="value_<?= $value->pk(); ?>"
                            name="feature[<?= $feature->pk(); ?>][<?= $value->pk(); ?>]"
                            value="<?= $value->pk(); ?>"
                            />
                        <i class="glyphicon glyphicon-certificate" style="background-color:<?= $value->code ?>;"></i>
                        <?= $value->name; ?>
                        <? if (isset($units[$value->unit_id])): ?>
                            <span class="b-filter-item__unit"><?= $units[$value->unit_id]->abbr ?></span>
                        <? endif ?>
                    </label>
                </div>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>