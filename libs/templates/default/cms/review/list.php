<div class="b-page-show b-reviews">

    <h1 class="b-page-show__title b-title b-reviews__title">
        Отзывы
    </h1>


    <a class="b-btn" href="<?= link_to('review_add') ?>">Добавить отзыв</a>


    <? if (count($reviews)): ?>
        <? foreach ($reviews as $item): ?>
            <div class="b-review">
                <div class="b-review__date">
                    <?= $item->name; ?>
                </div>
                <div class="b-review__name">
                    <?= $item->name; ?>
                </div>
                <div class="b-review__text">
                    <?= $item->text; ?>
                </div>
            </div>
        <? endforeach ?>
        <?= $pagination->render() ?>
    <? else: ?>
        <h2>В данной момент нет отзывов</h2>
    <? endif; ?>


</div>


