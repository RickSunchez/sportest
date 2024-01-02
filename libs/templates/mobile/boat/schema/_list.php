<? if (count($schemes)): ?>
    <ul class="b-cats-sub-inline">
        <? foreach ($schemes as $schema): ?>
            <li class="b-cats-sub-inline__item">
                <a class="b-cats-sub-inline__link"
                   href="<?= link_to_city('schema_index', array('id' => $schema->pk(), 'url' => $schema->url)); ?>">
                    <?= $schema->name ?>
                </a>
            </li>
        <? endforeach; ?>
    </ul>
<? endif; ?>
