<article class="b-page-show b-page-show_text">
    <header class="b-page-show__header">
        <h1 class="b-page-show__title"><?= $article->name ?></h1>
    </header>
    <time class="b-page-show__data"><?= \Delorius\Core\DateTime::dateFormat($article->date_cr) ?></time>
    <section class="b-page-show__text b-text">
        <?= $article->text ?>
    </section>
    <section class="b-page-show__share">
        <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
        <script src="//yastatic.net/share2/share.js"></script>
        <div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,gplus"></div>
    </section>
</article>

