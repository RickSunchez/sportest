<? if (count($categories[0])): ?>
    <div class="b-menu-sub">
        <ul class="b-category-menu">
            <? foreach ($categories[0] as $cat): ?>
                <li class="b-category-menu">
                    <a href="<?= $cat['link'] ?>">
                        <?= $cat['name'] ?>
                        <i class="fa fa-angle-right"></i>
                    </a>
                </li>
            <? endforeach; ?>
        </ul>
    </div>
<? endif; ?>