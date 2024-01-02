<div class="b-page-show b-events">
    <? if ($events): ?>
        <h1 class="b-title b-page-show__title b-events__title">Новости</h1>
        <? foreach ($events as $item): ?>
            <div class="b-events-item b-events__item">


                <div class="b-events-item__left <?= isset($images[$item->pk()]) ? 'b-events-item__left_isset' : '' ?>">
                    <a class="b-link " href="<?= $item->link() ?>" title="<?= $this->escape($item->name);?>">
                        <? if (isset($images[$item->pk()])): ?>
                            <img class="b-img b-events-item__img" src="<?= $images[$item->pk()]->preview; ?>"
                                 alt="<?= $this->escape($item->name); ?>">
                        <? else: ?>
                            <div class="b-no-photo b-events-item__no-foto"></div>
                        <? endif; ?>
                    </a>
                </div>


                <div class="b-events-item__right">
                <span class="b-events-item__date">
                    <?= date('d.m.Y', $item->date_cr); ?>
                </span>

                    <h2 class="b-events-item__title">
                        <a title="<?= $this->escape($item->name);?>" class="b-link b-events-item__link_title" href="<?= $item->link() ?>"><?= $item->name; ?></a>
                    </h2>

                    <div class="b-text b-events-item__preview">
                        <?= $item->preview; ?>
                    </div>
                    <a class="b-link b-events-item__link_footer" href="<?= $item->link() ?>">
                        Подробнее ...
                    </a>
                </div>


            </div>
        <? endforeach ?>
        <?= $pagination->render() ?>
    <? else: ?>
        <h2>В данной момент нет событий</h2>
    <? endif; ?>
</div>
