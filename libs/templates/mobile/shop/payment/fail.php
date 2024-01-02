<div class="b-page-show ">
    <div class="b-text b-page-show__text">
        <h1>Ошибка оплаты</h1>

        <div>
            <? if ($fail): ?>
                <?= $fail ?>
            <? else: ?>
                <p>Ваша оплата не прошла.</p>
            <? endif ?>
        </div>
    </div>
</div>