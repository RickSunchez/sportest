<? if (count($articles)): ?>
    <section class="b-widget">
        <h2 class="b-widget__title">
            Статьи
        </h2>

        <div class="b-widget__body">
            <? foreach ($articles as $item): ?>

                <div class="b-widget__item">

                    <div data-href="<?=$item->link()?>" class="l-widget__image <?= isset($images[$item->pk()]) ? 'b-widget__image_isset' : '' ?> ">
                        <? if (isset($images[$item->pk()])): ?>
                            <img class="b-widget__image"
                                 src="<?= $images[$item->pk()]->preview; ?>"
                                 alt="<?= $item->name; ?>"/>
                        <? else: ?>
                            <div class="i-no-photo i-no-photo_widget"></div>
                        <? endif; ?>
                    </div>


                    <div class="l-widget__info">
                        <div class="b-widget__date"><?= date('d.m.y', $item->date_cr); ?></div>
                        <a class="b-widget__link" href="<?= $item->link(); ?>">
                            <span class="b-widget__name"> <?= $item->name ?></span>
                        </a>
                    </div>


                </div>

            <? endforeach; ?>
        </div>
        <div class="b-widget__footer">
            <a class="b-widget__all" href="<?= link_to('articles') ?>">Все статьи</a>
        </div>
    </section>
<? endif; ?>