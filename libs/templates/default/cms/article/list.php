<div class="b-articles">
    <? if ($articles): ?>
        <h1 class="b-title b-articles__title">Статьи</h1>
        <? foreach ($articles as $item): ?>
            <div class="b-article-item b-articles__item">


                <div class="b-article-item__left <?= isset($images[$item->pk()]) ? 'b-article-item__left_isset' : '' ?>">
                    <a class="b-link " href="<?= $item->link() ?>" title="<?= $this->escape($item->name);?>">
                        <? if (isset($images[$item->pk()])): ?>
                            <img class="b-img b-article-item__img" src="<?= $images[$item->pk()]->preview; ?>"
                                 alt="<?= $this->escape($item->name); ?>">
                        <? else: ?>
                            <div class="b-no-photo b-article-item__no-foto"></div>
                        <? endif; ?>
                    </a>
                </div>


                <div class="b-article-item__right">
                    <span class="b-article-item__date">
                        <?= date('d.m.Y', $item->date_cr); ?>
                    </span>
                    <a title="<?= $this->escape($item->name);?>" class="b-link b-article-item__link_title" href="<?= $item->link() ?>">
                        <h2 class="b-article-item__title">
                            <?= $item->name; ?>
                        </h2>
                    </a>
                    <div class="b-text b-article-item__preview">
                        <?= $item->preview; ?>
                    </div>
                    <a class="b-link b-article-item__link_footer" href="<?= $item->link() ?>">
                        Подробнее ...
                    </a>
                </div>


            </div>
        <? endforeach ?>
        <?= $pagination->render() ?>
    <? else: ?>
        <h2>В данной момент нет статей</h2>
    <? endif; ?>
</div>

