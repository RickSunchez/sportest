<h1>Ошибка оплаты</h1>
<div>
    <? if ($fail): ?>
        <?= $fail ?>
        <? else: ?>
        <p>Ваша оплата не прошла.</p>
    <? endif ?>
</div>