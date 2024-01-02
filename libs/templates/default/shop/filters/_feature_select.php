<? if (count($values)): ?>
    <div class="b-filter-item b-filter-item_select">
    <div class="b-filter-item__title"><?= $filter->name ?></div>
    <div class="b-filter-item__layout">
    <select class="b-filter-item__select" name="feature[<?= $feature->pk(); ?>]">
        <option>Все</option>
        <? foreach ($values as $value): ?>
            <option
                value="<?= $value->pk(); ?>">
                <?= $value->name; ?>
                <? if (isset($units[$value->unit_id])): ?>
                    <?= $units[$value->unit_id]->abbr ?>
                <? endif ?>
            </option>
        <? endforeach; ?>
    </select>
    </div>
<? endif; ?>