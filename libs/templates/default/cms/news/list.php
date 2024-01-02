<div class="b-page-show b-news-list">
    <? if ($news): ?>
        <h1 class="b-title b-page-show__title b-news-list__title">Новости</h1>
        <? foreach ($news as $item): ?>
            <div class="b-news-item b-news-list__item">


                <div class="b-news-item__left <?= isset($images[$item->pk()]) ? 'b-news-item__left_isset' : '' ?>">
                    <a class="b-link " href="<?= $item->link() ?>" title="<?= $this->escape($item->name);?>">
                        <? if (isset($images[$item->pk()])): ?>
                            <img class="b-img b-news-item__img" src="<?= $images[$item->pk()]->preview; ?>"
                                 alt="<?= $this->escape($item->name); ?>">
                        <? else: ?>
                            <div class="b-no-photo b-news-item__no-foto"></div>
                        <? endif; ?>
                    </a>
                </div>


                <div class="b-news-item__right">
                <span class="b-news-item__date">
                    <?= date('d.m.Y', $item->date_cr); ?>
                </span>

                    <h2 class="b-news-item__title">
                        <a title="<?= $this->escape($item->name);?>" class="b-link b-news-item__link_title" href="<?= $item->link() ?>"><?= $item->name; ?></a>
                    </h2>

                    <div class="b-text b-news-item__preview">
                        <?= $item->preview; ?>
                    </div>
                    <a class="b-link b-news-item__link_footer" href="<?= $item->link() ?>">
                        Подробнее ...
                    </a>
                </div>


            </div>
        <? endforeach ?>
        <?= $pagination->render() ?>
    <? else: ?>
        <h2>В данной момент нет новостей</h2>
    <? endif; ?>
</div>
