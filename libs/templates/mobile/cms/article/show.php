<article class="b-page-show" itemscope itemtype="http://schema.org/Article" role="main">
    <?= $this->partial('html/meta/image', array('image' => $image)) ?>
    <?= $this->partial('html/meta/author') ?>
    <meta itemprop="mainEntityOfPage" content="<?= $control->urlScript->getAbsoluteUrlNoQuery() ?>">
    <meta itemprop="dateModified" content="<?= date('Y-m-d', $article->date_cr) ?>">
    <div itemprop="datePublished" class="meta"><?= date(DATE_ATOM, $article->date_cr) ?></div>
    <header class="b-page-show__header">
        <h1 itemprop="name headline" class="b-page-show__title">
            <?= $article->name; ?>
        </h1>
    </header>
    <div class="b-article__misc">
        <div class="b-article__date"><?= \Delorius\Core\DateTime::dateFormat($article->date_cr) ?></div>
        <div class="b-article__views"><i
                    class="glyphicon glyphicon-eye-open"></i><?= $article->views ?></div>
        <div class="b-article__shared">
            <script src="//yastatic.net/es5-shims/0.0.2/es5-shims.min.js"></script>
            <script src="//yastatic.net/share2/share.js"></script>
            <div class="ya-share2" data-services="collections,vkontakte,facebook,odnoklassniki,gplus"></div>

        </div>
    </div>
    <div itemprop="articleBody" class="b-page-show__text b-text">
        <?= $article->text; ?>
    </div>
</article>
