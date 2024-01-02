<? if (count($menu[0])): ?>
    <ul class="b-navigation__menu">
        <? foreach ($menu[0] as $item): ?>
            <li><a href="<?= $item->link() ?>"><?= $item->name ?></a></li>
        <? endforeach ?>
        <?= $this->action('Boat:Store:PageCity:menu') ?>
    </ul>
<? endif; ?>