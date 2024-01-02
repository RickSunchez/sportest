<? if (count($values)): ?>
    <div class="b-filter-item b-filter-item_link">
        <div class="b-filter-item__title"><?= $filter->name ?></div>
        <div class="b-filter-item__layout">

            <? foreach ($values as $value): ?>
            <div class="b-filter-item__value b-filter-item__value_link">
                <a class="b-link" href="<?= $url->setQuery(_sf('feature[{0}]={1}',$feature->pk(),$value->pk()))?>" >
                    <?= $value->name; ?>
                    <? if (isset($units[$value->unit_id])): ?>
                       <span class="b-filter-item__unit"> <?= $units[$value->unit_id]->abbr ?></span>
                    <? endif ?>
                </a>
            </div>
            <? endforeach; ?>

        </div>
        </div>
    </div>
<? endif; ?>