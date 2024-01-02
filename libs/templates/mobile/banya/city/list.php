<article>
    <header class="b-category__header">
        <h1>Список городов</h1>
    </header>

    <div class="b-alphabet">
        <? foreach ($alpha as $a): ?>
            <? if (isset($cities[$a])): ?>
                <div class="b-alphabet__item">
                    <div class="b-alphabet__letter">
                        <?= $a ?>
                    </div>
                    <div class="b-alphabet__cities">
                        <? foreach ($cities[$a] as $item): ?>
                            <? if ($item['main']): ?>
                                <a class="b-alphabet__link"
                                   href="/">
                                    <?= $item['name'] ?>
                                </a>
                            <? else: ?>
                                <a class="b-alphabet__link"
                                   href="<?= link_to('homepage_city', array('city_url' => $item['url'])) ?>">
                                    <?= $item['name'] ?>
                                </a>
                            <? endif; ?>
                        <? endforeach; ?>
                    </div>

                </div>
            <? endif; ?>
        <? endforeach; ?>
    </div>

</article>

