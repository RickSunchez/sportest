<article class="b-gallery-show">
    <header>
        <h1>Фотогалереи</h1>
    </header>
    <ul class="b-galleries">

        <? foreach ($galleries as $gallery): ?>
            <? if (isset($images[$gallery->pk()])): ?>

                <li class="b-gallery-item">
                    <a title="Показать <?= $gallery->name; ?>" class="b-link"
                       href="<?= link_to('gallery', array('id' => $gallery->pk())); ?>">
                        <img src="<?= $images[$gallery->pk()]->preview; ?>"/>
                    </a>

                    <div class="b-gallery-item__name"><?= $gallery->name; ?></div>
                </li>

            <? endif; ?>
        <? endforeach; ?>

    </ul>
</article>