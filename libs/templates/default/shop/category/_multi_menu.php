<ul>
    <? foreach ($categories[$menu_id] as $item): ?>
        <li class=" <?= $menu_id == 0 ? 'item' : 'ditem' ?>   <?= ($selfCategoryId == $item['id']) ? 'active' : ''; ?> <?= $item['children'] ? 'isset' : '' ?> <?= isset($categories[$item['id']]) ? 'select' : '' ?> ">
            <a href="<?= (isset($categories[$item['id']]))?'javascript:;':$item['link'] ?>">
                <span><?= $item['name'] ?></span>
            </a>
            <? if (count($categories[$item['id']])): ?>
                <?= $this->partial('shop/category/_multi_menu', array('categories' => $categories, 'menu_id' => $item['id'], 'parentIds' => $parentIds, 'selfCategoryId' => $selfCategoryId)); ?>
            <? endif; ?>
        </li>
    <? endforeach; ?>
</ul>
