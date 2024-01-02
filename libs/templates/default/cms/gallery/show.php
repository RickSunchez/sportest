<article class="b-gallery-show">
    <header>
        <h1><?= $gallery->name ?></h1>
    </header>

    <section class="b-images">

        <? if (count($images)): ?>
            <ul class="b-galleries js-gallery">
                <? foreach ($images as $key => $image): ?>
                    <li class="b-gallery-item">
                        <a href="<?= $image->normal ?>" title="#<?= ($key + 1) ?> <?= $image->name ?>">
                            <img src="<?= $image->preview ?>" alt="<?= $image->name ?>">
                        </a>
                        <? if ($image->name): ?>
                            <div class="b-gallery-item__name">
                                <?= ($key + 1) ?>. <?= $image->name ?>
                            </div>
                        <? endif; ?>
                    </li>
                <? endforeach; ?>
            </ul>
        <? endif; ?>


    </section>
</article>