<? if (count($videos)): ?>
    <aside class="b-aside b-aside_related" role="presentation">

        <h1 class="b-aside__title">Другое видео</h1>
        <div class="b-aside__layout b-related__layout">


        <? foreach ($videos as $video): ?>
            <article class="b-aside__item b-aside__item_25 ">
                <a href="<?= $video->link()?>">
                    <? if (isset($images[$item->pk()])): ?>
                        <img class="b-img" src="<?= $images[$item->pk()]->preview; ?>"
                             alt="<?= $this->escape($item->name); ?>">
                    <? else: ?>
                        <div class="b-no-photo b-video-item__no-foto"></div>
                    <? endif; ?>
                </a>
                <time>
                    Опубликовано: <?= \Delorius\Core\DateTime::dateFormat($video->date_cr, true); ?>
                </time>
                <h1><a href="<?= $video->link()?>"><?= $video->name?></a></h1>
            </article>
        <? endforeach; ?>



        </div>
    </aside>

<? endif; ?>