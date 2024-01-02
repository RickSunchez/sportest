<? if (count($collections)): ?>
    <div class="sr-block sr-block_collection">
        <div class="b-collection__name"><?= $filter->name ? $filter->name : 'Подборки'; ?></div>
        <div class="b-collection__body">
            <? foreach ($collections as $item): ?>
                <div class="b-collection__value">

                    <a href="<?= link_to_city('shop_category_collection', array(
                        'id' => $item['id'], 'url' => $item['url'])); ?>">
                        <?= $item['name'] ?></a>
                </div>
            <? endforeach; ?>
        </div>
    </div>
<? endif; ?>

