<article class="b-page-show" itemscope itemtype="http://schema.org/ItemList">
    <header class="b-page-show__header">
        <h1 itemprop="name" class="b-page-show__title">Полезная информация</h1>
    </header>


    <div class="b-page-show__list / hListing">
        <? if (count($articles)): ?>
            <? foreach ($articles as $item): ?>

                <section class="b-article__item / item"
                         itemprop="itemListElement" itemscope itemtype="http://schema.org/ListItem">
                    <meta itemprop="position" content="<?= $key + 1; ?>">
                    <div data-href="<?= $item->link() ?>" class="b-article__image"
                         data-image="<?= $images[$item->pk()]->preview; ?>"></div>

                    <div class="b-article__info">

                        <h2 class="b-article__name" itemprop="name">
                            <a itemprop="url" class="b-article__link"
                               href="<?= $item->link(); ?>"><?= $item->name ?></a>
                        </h2>
                        <div class="b-article__misc">
                            <div class="b-article__date"><?= \Delorius\Core\DateTime::dateFormat($item->date_cr) ?></div>
                            <div class="b-article__views"><i
                                        class="glyphicon glyphicon-eye-open"></i><?= $item->views ?></div>
                        </div>
                    </div>

                </section>

            <? endforeach; ?>

            <?= $pagination ?>

        <? else: ?>

            <h2>В данный момент нет статей</h2>

        <? endif; ?>

    </div>
</article>
