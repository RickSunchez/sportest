<? if (count($pages)): ?>
    <div class="b-page-sub__btn" data-open="page-sub">Меню</div>
    <aside data-model="page-sub" class="b-page-model">
        <div class="b-model__header">
            <button class="b-model__close js-model--close"></button>
            <?= $page->short_title ?>
        </div>
        <div class="b-model__layout">

            <? foreach ($pages as $p): ?>
                <div class="b-page-sub__item <?= is_current_path($p->link(), true) ? 'active' : ''; ?>">
                    <a href="<?= $p->link(); ?>"><?= $p->short_title; ?></a>
                </div>
            <? endforeach; ?>

        </div>
    </aside>
<? endif; ?>