<nav class="b-menu-categories">
    <ul class="b-menu-category__layout">
        <? foreach ($categories[$menu_id] as $key => $item): ?>
            <li class="b-menu-category__item <?= $item['children'] ? 'isset' : '' ?> <?= ($selfCategoryId == $item['id']) ? 'active' : ''; ?> ">
                <a href="<?= $item['link']; ?>"><?= $item['name'] ?></a>
                <? if (count($categories[$item['id']])): ?>
                    <?= $this->partial('cms/news/_categories_sub', array('categories' => $categories, 'menu_id' => $item['id'], 'parentIds' => $parentIds, 'selfCategoryId' => $selfCategoryId)); ?>
                <? endif; ?>
            </li>
        <? endforeach ?>
    </ul>
</nav>
