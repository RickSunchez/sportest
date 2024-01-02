<? if (count($articles)): ?>
    <div class="b-articles-widget">
        <div class="b-articles-widget__title">Статьи</div>
        <div class="b-articles-widget__list">
            <? foreach ($articles as $item): ?>
                <div class="b-articles-widget__item">
                    <div class="b-articles-widget__date"><?= \Delorius\Core\DateTime::dateFormat($item->date_cr) ?></div>
                    <a class="b-articles-widget__name" href="<?= $item->link(); ?>"><?= $item->name ?></a>
                </div>
            <? endforeach; ?>

        </div>
        <a href="<?= link_to('articles') ?>">Больше статей</a>
    </div>
<? endif; ?>
