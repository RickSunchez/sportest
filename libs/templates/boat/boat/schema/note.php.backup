<article class="b-category b-category_full" itemscope itemtype="http://schema.org/ItemList">

    <header class="b-category__header">
        <h1 itemprop="name" class="b-category__title">

            <? if ($note->title): ?>
                <?= $note->title ?>
            <? else: ?>
                <?= $note->name ?>. <?= $vendor ? $vendor->name : '' ?> <?= $schema->name ?>
            <? endif; ?>

        </h1>
    </header>


    <div class="b-table b-note">

        <div class="b-table-cell b-note-image">

            <? if ($image->loaded()): ?>
                <img src="<?= $image->normal ?>"
                     alt="<?= $this->escape($note->name); ?>">
            <? endif; ?>

        </div>
        <div class="b-table-cell  b-note-list">
            <table class="table table-condensed table-bordered table-hover table-striped ">
                <tr class="info">
                    <th width="20">No</th>
                    <th class="i-center-td">Название</th>
                    <th width="100" class="i-center-td">Цена</th>
                    <th width="20"></th>
                </tr>
                <? foreach ($items as $item): ?>
                    <? if (isset($products[$item->pid])): ?>
                        <tr>
                            <td class="i-center-td"><?= $item->number ?></td>
                            <td class="i-middle-td">
                                <div><?= $item->name ?></div>
                                <b><?= $item->article ?></b>
                            </td>
                            <td class="i-center-td">
                                <? if (isset($products[$item->pid]) &&
                                    $products[$item->pid]->status == 1 &&
                                    $products[$item->pid]->is_amount == 1): ?>
                                    <?= $products[$item->pid]->getPrice(); ?>
                                <? else: ?>
                                    <?= $products[$item->pid] ? $products[$item->pid]->getPrice() : ""; ?>
                                    <span class="b-note-none">Нет в наличии</span>
                                <? endif; ?>
                            </td>
                            <td class="i-middle-td">

                                <? if (isset($products[$item->pid]) &&
                                    $products[$item->pid]->status == 1 &&
                                    $products[$item->pid]->is_amount == 1): ?>

                                    <a class="b-btn b-btn_small" target="_blank"
                                       href="<?= $products[$item->pid]->link(); ?>">Купить</a>
                                <? else: ?>

                                    <a class="b-btn b-btn_small b-btn_order"
                                       href="<?= $products[$item->pid]->link(); ?>">Заказать</a>

                                    <!--                                <a class="b-btn b-btn_small b-btn_order" data-popup="#order"-->
                                    <!--                                   data-product="--><? //= $item->name ?><!-- --><? //= $item->article ?><!--"-->
                                    <!--                                   href="javascript:;">Заказать</a>-->
                                <? endif; ?>

                            </td>
                        </tr>
                    <? endif; ?>
                <? endforeach; ?>
            </table>
        </div>

    </div>


</article>