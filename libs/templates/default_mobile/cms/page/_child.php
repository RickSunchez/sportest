<? if (count($pages)): ?>
    <div class="b-widget b-page-child-widget">
        <h2 class="b-widget__title b-page-child-widget__title"><?=$parent->short_title?></h2>
        <ul class="b-page-child-widget__layout">
            <? foreach ($pages as $page): ?>
                <li class="b-page-child-widget__item">
                    <a class="b-link b-page-child-widget__link" href="<?= $page->link() ?>">
                        <?= $page->short_title ?>
                    </a>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<? endif; ?>


