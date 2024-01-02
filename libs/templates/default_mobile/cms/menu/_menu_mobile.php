<? if (count($menu[0])): ?>

    <div class="b-menu-horiz__layout">

        <? foreach ($menu[0] as $item): ?>

            <a class="b-link b-menu-horiz__link " href="<?= $item->link() ?>">
                <?= $item->name ?>
            </a>
            <?= $this->action('Shop:Catalog:Shop:sub', array('cid' => 0, 'theme' => 'menu')) ?>
        <? endforeach ?>

    </div>

<? endif; ?>