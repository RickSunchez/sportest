<?if(count($categories[$category_id])):?>
<ul class="b-menu-articles__layout b-menu-articles__layout_sub">
    <?foreach($categories[$category_id] as $item):?>
        <li class="b-menu-articles__item b-menu-articles__item_sub <?= $category_id == 0 ? 'item' : 'ditem' ?>   <?= ($selfCategoryId == $item['cid']) ? 'b-menu-articles__item_active' : ''; ?> <?= $item['children'] ? 'b-menu-articles__item_isset' : '' ?> <?= isset($categories[$item['cid']]) ? 'b-menu-articles__item_select' : '' ?> ">
            <a class="b-link b-menu-articles__link b-menu-articles__link_sub " href="<?= $item['link']; ?>">
                <span><?= $item->name?></span>
            </a>
            <? if (count($categories[$item['cid']])): ?>
                <?= $this->partial('cms/article/_list_category_sub', array('categories' => $categories, 'category_id' => $item['cid'], 'parentIds' => $parentIds, 'selfCategoryId' => $selfCategoryId)); ?>
            <? endif; ?>
        </li>
    <?endforeach?>
</ul>
<?endif;?>