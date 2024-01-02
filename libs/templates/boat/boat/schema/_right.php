<? if (count($notes)): ?>
    <div class="b-right-list">
        <h3 class="b-right-list__name">Подходят для:</h3>
        <? foreach ($notes as $note): ?>
            <div class="b-note-item">

                <? if ($schema = ($schemes[$note->sid])): ?>

                    <a class="b-note-item__name"
                       href="<?= link_to_city('schema_index', array('id' => $schema->pk(), 'url' => $schema->url)); ?>">

                        <? if ($vendor = ($vendors[$schema->vid])): ?>
                            <?= $vendor->name ?>
                        <? endif; ?>

                        <?= $schema->name ?>
                    </a> -
                <? endif; ?>

                <a class="b-note-item__li"
                   href="<?= link_to_city('schema_note', array('id' => $note->pk(), 'url' => $note->url)); ?>"><?= $note->name ?></a>

            </div>
        <? endforeach; ?>

    </div>
<? endif; ?>
