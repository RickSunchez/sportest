<ul class="b-menu-sub-category__layout">
    <? foreach ($categories[$menu_id] as $item): ?>
        <li class="b-menu-category__item <?= $item['children'] ? 'isset' : '' ?> <?= ($selfCategoryId == $item['id']) ? 'active' : ''; ?>">
            <a href="<?= $item['link']; ?>"><?= $item['name'] ?></a>
            <? if (count($categories[$item['id']])): ?>
                <?= $this->partial('cms/video/_categories_sub', array('categories' => $categories, 'menu_id' => $item['id'], 'parentIds' => $parentIds, 'selfCategoryId' => $selfCategoryId)); ?>
            <? endif; ?>
        </li>
    <? endforeach ?>
</ul>

