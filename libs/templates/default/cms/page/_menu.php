<? if (count($pages)): ?>
    <aside class="b-aside b-aside_page-list">
        <h1 class="b-aside__title b-aside__title_left b-aside__title_left_b0"><?= $parent->short_title ?></h1>

        <div class="b-aside__layout b-aside__layout_page">
            <div class="list-group">
                <? foreach ($pages as $page): ?>
                    <a href="<?= $page->link() ?>" class="list-group-item"><?= $page->short_title ?></a>
                <? endforeach; ?>
            </div>
        </div>
    </aside>
<? endif; ?>