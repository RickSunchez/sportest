<? if (count($categories)): ?>
    <div class="main-categories-title">Каталог товаров</div>
    <ul class="main-categories-list">
        <? foreach ($categories as $category): ?>
            <li class="main-categories__item">
                <a title="<?= $this->escape($category['name']) ?>" class="main-categories__item-image"
                   href="<?= $category['link'] ?>">
                    <? if (isset($images[$category['id']])): ?>
                        <img src="/thumb/196/<?= $images[$category['id']]['image_id'] ?>"
                             alt="<?= $this->escape($category['name']) ?>">
                    <? else: ?>
                        <img src="/source/images/no.png"
                             alt="<?= $this->escape($category['name']) ?>">
                    <? endif; ?>
                </a>


                <a class="main-categories__item-name" class="name"><?= $category['name'] ?></a>

                <div class="main-categories__item-product">
                    <?= $category['goods'] ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'товар', 'товара', 'товаров') ?>
                </div>
            </li>
        <? endforeach ?>
    </ul>

<? endif; ?>
