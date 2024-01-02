<? if (count($products)): ?>
    <ul class="b-subcategories-list">
        <? foreach ($products as $key => $item): ?>

            <li class="b-subcategory__item">
                <a class="b-subcategory__image"
                   href="<?= $item->link(); ?>">
                    <? if ($item->image): ?>
                        <img src="/thumb/150/<?= $item->image->image_id ?>"
                             alt="<?= $this->escape($item->name); ?>">
                    <? else: ?>
                        <img src="/source/images/no.png" alt="">
                    <? endif; ?>
                </a>
                <a class="b-subcategory__link" href="<?= $item->link(); ?>">
                    <?= $item->name; ?>
                </a>

                <div class="b-subcategory__price"><?= $item->getPrice() ?></div>
            </li>

        <? endforeach; ?>
    </ul>

    <? if ($count): ?>
        <a class=" b-subcategories__yet-link" href="<?= $url; ?>">+
            еще <?= $count; ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'товар', 'товара', 'товаров') ?></a>
    <? endif; ?>

<? endif; ?>
