<? foreach ($goods as $item): ?>
    <div class="b-carusel__item">
        <a title="<?= $item->name ?>" href="<?= link_to('shop_goods', array('id' => $item->pk(),'url' => $item->url));?>">
            <img class="b-carusel__item_img i-radius"
                 src="<?= $images[$item->pk()] ? $images[$item->pk()]->preview : "/source/images/zero.gif" ?>"
                 alt="<?= $item->name ?>"/>
        </a>

        <div class="b-carusel__name"><?= \Delorius\Utils\Strings::truncate($item->name, 30); ?></div>
    </div>
<? endforeach; ?>