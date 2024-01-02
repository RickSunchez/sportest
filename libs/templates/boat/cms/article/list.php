<div class="b-page-show ">
    <header class="b-page-show__header">
        <h1 class="b-page-show__title">Полезная информация</h1>
    </header>
    <? if (count($articles)): ?>
        <div class="b-page-show__list-box b-page-show__list-box_50">

            <? foreach ($articles as $item): ?>
                <section class="b-article__item">
                    <a href="<?= $item->link(); ?>" title="<?= $this->escape($item->name); ?>"
                       class="b-article__item-image">
                        <? if ($images[$item->pk()]): ?>
                            <img src="<?= $images[$item->pk()]->preview ?>" alt="<?= $this->escape($item->name); ?>">
                        <? else: ?>
                            <img src="/source/images/no.png" alt="">
                        <? endif; ?>
                    </a>

                    <div class="b-article__item-info">
                        <div class="b-article__item-layout">
                            <div
                                class="b-article__item-date"><?= \Delorius\Core\DateTime::dateFormat($item->date_cr) ?></div>
                            <h2 class="b-article__item-name">
                                <a title="<?= $this->escape($item->name); ?>"
                                   href="<?= $item->link(); ?>"><?= $item->name; ?></a>
                            </h2>

                            <p class="b-article__item-preview"><?= $item->getPreview(150); ?></p>
                        </div>
                    </div>
                </section>
            <? endforeach; ?>

        </div>

        <?= $pagination ?>

    <? else: ?>
        <h2></h2>
    <? endif; ?>

</div>

