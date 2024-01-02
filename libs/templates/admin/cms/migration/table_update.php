<h1>Обновление таблиц</h1>

<? if (count($update)): ?>
    <p>Список добавленых таблиц:</p>
    <? foreach ($update as $key => $name): ?>
        <p>
            <?= ($key + 1) ?>. <?= $name ?>
        </p>
    <? endforeach; ?>
<? endif; ?>
