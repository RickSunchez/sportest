<div class="b-page-show b-news-show">
        <span class="b-page-show__date b-news-show__date">
            <?=date('d.m.Y', $news->date_cr);?>
        </span>
        <h1 class="b-title b-page-show__title b-news-show__title">
            <?=$news->name;?>
        </h1>
        <div class="b-page-show__text b-text b-news-show__text">
            <?=$news->text;?>
        </div>
</div>

