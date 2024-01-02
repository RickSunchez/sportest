<? if (count($events)): ?>
    <div class="b-widget b-event-widget">
        <div class="b-widget__title b-event-widget__title">
            События
        </div>
        <div class="b-widget__body b-event-list-widget">
            <? foreach ($events as $item): ?>

                <div class="b-event-item-widget">
                    <div class="b-event-item-widget__date"><?= date('d.m.y', $item->date_cr); ?></div>
                    <a class="b-link b-event-item-widget__link" href="<?= $item->link();?>">
                        <span class="b-event-item-widget__name"> <?= $item->name ?></span>
                    </a>
                    <div class="b-event-item-widget__preview"><?= \Delorius\Utils\Strings::truncate($item->preview,55); ?></div>

                </div>

            <? endforeach; ?>
        </div>
        <div class="b-widget__footer b-event-widget__footer">
            <a class="b-link b-event-widget__link_all" href="<?= link_to('event') ?>">Все события</a>
        </div>
    </div>
<? endif; ?>