<? if (count($news)): ?>
    <div class="b-widget b-news-widget">
        <div class="b-widget__title b-news-widget__title">
            НОВОСТИ
        </div>
        <div class="b-widget__body b-news-list-widget">
            <? foreach ($news as $item): ?>

                    <div class="b-news-item-widget">


                        <div class="b-news-item-widget__left <?= isset($images[$item->pk()]) ? 'b-news-item-widget__left_isset' : '' ?> ">
                            <? if (isset($images[$item->pk()])): ?>
                                <img class="b-image b-news-item-widget__image"
                                     src="<?= $images[$item->pk()]->preview; ?>"
                                     alt="<?= $item->name; ?>" />
                            <? else: ?>
                                <div class="b-no-photo b-news-item-widget__no-foto"></div>
                            <? endif; ?>
                        </div>


                        <div class="b-news-item-widget__right">
                            <div class="b-news-item-widget__date"><?= date('d.m.y', $item->date_cr); ?></div>
                            <a class="b-link b-news-item-widget__link" href="<?= $item->link();?>">
                                <span class="b-news-item-widget__name"> <?= $item->name ?></span>
                            </a>
                            <div class="b-news-item-widget__preview"><?= \Delorius\Utils\Strings::truncate($item->preview,55); ?></div>
                        </div>


                    </div>

            <? endforeach; ?>
        </div>
        <div class="b-widget__footer b-news-widget__footer">
            <a class="b-link b-news-widget__link_all" href="<?= link_to('news') ?>">Все новости</a>
        </div>
    </div>
<? endif; ?>