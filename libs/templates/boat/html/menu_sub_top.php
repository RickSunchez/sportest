<? if (count($categories[0])): ?>
    <div class="b-menu-sub">
        <div class="b-menu-sub__layout">

            <? foreach ($categories[0] as $parent): ?>
                <div class="b-menu-sub__item">

                    <a class="b-menu-sub__item-name" href="<?= $parent['link'] ?>">
                        <? if (isset($images[$parent['id']])): ?>
                            <img src="/thumb/40/<?= $images[$parent['id']]['image_id'] ?>" alt="<?= $this->escape($parent['name'])?>">
                        <? endif; ?>
                        <span><?= $parent['name'] ?></span>
                    </a>

                    <? if (count($categories[$parent['id']])): ?>

                        <div class="b-menu-sub__item-list">

                            <? foreach ($categories[$parent['id']] as $key => $cat): ?>
                                <? if ($key <= 7): ?>
                                    <span class="b-menu-sub__item-sub">
                                    <a class="b-menu-sub__item-link" href="<?= $cat['link'] ?>">
                                        <?= $cat['name'] ?></a> <?= $cat['goods']; ?></span>
                                    <? unset($categories[$parent['id']][$key]) ?>
                                <? endif; ?>
                            <? endforeach; ?>

                            <? if ($count = count($categories[$parent['id']])): ?>

                                <span class="b-menu-sub__item-subs">
                                    <a class="b-menu-sub__item-yet-link"
                                       href="javascript:;">еще <?= $count ?> <?= \Delorius\Utils\Strings::pluralForm($count, 'категория', 'категории', 'категорий') ?></a>


                                    <? foreach ($categories[$parent['id']] as $key => $cat): ?>

                                        <span class="b-menu-sub__item-sub">
                                        <a class="b-menu-sub__item-link" href="<?= $cat['link'] ?>">
                                            <?= $cat['name'] ?></a> <?= $cat['goods']; ?></span>
                                    <? endforeach; ?>
                                </span>
                            <? endif; ?>

                        </div>
                    <? endif; ?>

                </div>
            <? endforeach; ?>

        </div>
    </div>
<? endif; ?>
