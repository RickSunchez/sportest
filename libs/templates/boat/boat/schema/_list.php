<? if (count($schemes)): ?>
    <ul class="b-cats-sub-inline">
        <? foreach ($schemes as $schema): ?>
            <li class="b-cats-sub-inline__item _schema">
                <a class="b-cats-sub-inline__image-link"
                   href="<?= link_to_city('schema_index', array('id' => $schema->pk(), 'url' => $schema->url)); ?>">
                    <? if ($image = ($images[$schema->pk()])): ?>
                        <img src="<?= $image->preview ?>" alt="">
                    <? else: ?>
                        <img src="/source/images/no.png" alt="">
                    <? endif; ?>
                </a>

                <a class="b-cats-sub-inline__link _schema"
                   href="<?= link_to_city('schema_index', array('id' => $schema->pk(), 'url' => $schema->url)); ?>">
                    <?= $schema->name ?>
                </a>
            </li>
        <? endforeach; ?>
    </ul>
<? endif; ?>