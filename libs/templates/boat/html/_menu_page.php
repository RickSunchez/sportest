<? if (count($pages)): ?>
    <aside class="b-page-menu">
        <h3 class="b-page-menu__name"><?= $page->short_title ?></h3>
        <ul class="b-page-menu__list">
            <? foreach ($pages as $p): ?>
                <li data-id="<?= $p->pk() ?>"
                    class="b-page-menu__list-it <?= is_current_path($p->link(), true) || ($select_page_id == $p->pk()) ? 'active' : ''; ?>">
                    <a
                            href="<?= $p->link(); ?>"><?= $p->short_title; ?></a>


                    <? if ($select_page_id == $p->pk() && count($child_pages)): ?>
                        <ul class="b-page-menu__list-child">
                            <? foreach ($child_pages as $c_p): ?>
                                <li data-id="<?= $c_p->pk() ?>"
                                    class="b-page-menu__list-child-it <?= is_current_path($c_p->link(), true) || ($select_child_page_id == $c_p->pk()) ? 'active_2' : ''; ?> ">
                                    <a href="<?= $c_p->link(); ?>"><?= $c_p->short_title ?></a>
                                </li>
                            <? endforeach; ?>
                        </ul>
                    <? endif; ?>


                </li>
            <? endforeach; ?>
        </ul>
    </aside>
<? endif; ?>
