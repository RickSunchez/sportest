<? if (count($menu[0])): ?>
    <div class="b-menu-horiz__layout">

        <? foreach ($menu[0] as $item): ?>
            <a class="m-menu__item" href="<?= $item->link() ?>">
                <?= $item->name ?>
            </a>
        <? endforeach ?>

        <?= $this->action('Boat:Store:PageCity:menu') ?>

    </div>
<? endif; ?>