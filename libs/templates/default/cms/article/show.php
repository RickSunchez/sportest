<div class="b-page-show b-article-show">
    <span class="b-page-show__date b-article-show__date">
        <?=date('d.m.Y', $article->date_cr);?>
    </span>
    <h1 class="b-page-show__title b-title b-article-show__title">
        <?=$article->name;?>
    </h1>
    <div class="b-page-show__text b-text b-article-show__text">
        <?=$article->text;?>
    </div>
</div>

