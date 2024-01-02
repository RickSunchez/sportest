<div class="b-page-show ">
    <div class="b-text b-page-show__text">
        <h1>Оплата прошла успешна!</h1>

        <div>
            <? if ($success): ?>
                <?= $success ?>
            <? else: ?>
                <p>Спасибо, Ваша оплата принята! Мы свяжемся с Вами в ближайшее время!</p>
            <? endif ?>
        </div>
    </div>
</div>