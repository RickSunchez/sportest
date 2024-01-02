<div class="b-video">
    <div class="b-video__layout">
        <?= $video->getEmbedCode('770', '430', true) ?>
    </div>
</div>
<div class="b-video__tags">
    <i class="glyphicon glyphicon-tags"></i> Метки:
    <a href="/">Боль</a>
    <a href="/">Вода</a>
    <a href="/">Тазик</a>
</div>
<article class="b-video__info b-container_pd10">

    <header class="b-video__header">
        <h1 class="h1"><?= $video->name ?></h1>
        <time class="time">Опубликовано: <?= \Delorius\Core\DateTime::dateFormat($video->date_cr, true); ?></time>
    </header>
    <section class="b-video__text b-text">
        <?= $video->text ?>
    </section>
</article>
