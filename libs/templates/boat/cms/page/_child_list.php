<? if (count($pages)): ?>

    <? foreach ($pages as $page): ?>
        <div class="b-page-child__item" data-href="<?= $page->link() ?>">
            <h3 class="b-page-child__name">
                <a class="b-page-child__link" href="<?= $page->link() ?>">
                    <?= $page->short_title ?>
                </a>
            </h3>
            <? if ($page->description): ?>
                <div class="b-page-child__text">
                    <?= \CMS\Core\Helper\Jevix\JevixEasy::Parser($page->description); ?>
                </div>
            <? endif; ?>
        </div>
    <? endforeach; ?>

<? endif; ?>


