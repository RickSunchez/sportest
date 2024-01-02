<article class="b-category b-category_full" itemscope itemtype="http://schema.org/ItemList">

    <header class="b-category__header">
        <h1 class="b-page__title">
            <? if ($schema->title): ?>
                <?= $schema->title ?>
            <? else: ?>
                <?= $vendor ? $vendor->name : '' ?> <?= $schema->name ?>
            <? endif; ?>
        </h1>
    </header>


    <ul class="b-schema-index">

        <? foreach ($notes as $note): ?>
            <li class="b-schema-index__item">
                <a title="<?= $this->escape($note->name); ?>" class="b-schema-index__item-image"
                   href="<?= link_to_city('schema_note', array('id' => $note->pk(), 'url' => $note->url)); ?>">
                    <? if (isset($images[$note->pk()])): ?>
                        <img src="<?= $images[$note->pk()]->preview ?>"
                             alt="<?= $this->escape($note->name); ?>">
                    <? else: ?>
                        <img src="/source/images/no.png" alt="">
                    <? endif; ?>
                </a>
                <a class="b-schema-index__item-name" class="name">
                    <?= $note->name ?></a>
            </li>
        <? endforeach; ?>


    </ul>


</article>