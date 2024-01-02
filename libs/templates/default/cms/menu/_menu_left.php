<? if (count($menu[0])): ?>
    <div class="b-menu-vert">
        <h3 class="b-menu-vert__title">Меню</h3>
        <ul class="b-menu-vert__layout">
            <? foreach ($menu[0] as $item): ?>
                <li class="b-menu-vert__item">
                    <a class="b-link b-menu-vert__link " href="<?= $item->link() ?>">
                        <span><?= $item->name ?></span>
                    </a>
                </li>
            <? endforeach ?>
        </ul>
    </div>
<? endif; ?>