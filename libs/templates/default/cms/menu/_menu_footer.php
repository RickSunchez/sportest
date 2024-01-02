<?if(count($menu[0])):?>
<div class="b-menu-horiz b-menu-horiz_footer">
        <ul class="b-menu-horiz__layout">
            <?foreach($menu[0] as $item):?>
                <li class="b-menu-horiz__item">
                    <a class="b-link b-menu-horiz__link " href="<?= $item->link()?>">
                        <span><?= $item->name?></span>
                    </a>
                </li>
            <?endforeach?>
        </ul>
</div>
<?endif;?>