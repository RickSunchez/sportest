<? if (count($tags)): ?>
    <div class="b-widget b-news-tags">
        <div class="b-widget__title b-news-tags__title">
            Метки
        </div>
        <div class="b-widget__body b-news-tags__list">
            <? foreach ($tags as $key => $tag): ?><? if ($key != 0): ?>,<? endif; ?> <a class="b-link b-news-tags__link" href="<?= link_to('news', array('tag' => $tag->name,'type'=>$type)) ?>"><?= $tag->name ?></a><? endforeach; ?>
        </div>
    </div>
<? endif; ?>

