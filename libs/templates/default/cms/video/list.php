<div class="b-page-show b-video-list">
    <? if ($videos): ?>
        <h1 class="b-title b-page-show__title b-video-list__title">Новости</h1>
        <? foreach ($videos as $item): ?>
            <div class="b-video-item b-video-list__item">


                <div class="b-video-item__left <?= isset($images[$item->pk()]) ? 'b-video-item__left_isset' : '' ?>">
                    <a class="b-link " href="<?= $item->link() ?>" title="<?= $this->escape($item->name); ?>">
                        <? if (isset($images[$item->pk()])): ?>
                            <img class="b-img b-video-item__img" src="<?= $images[$item->pk()]->preview; ?>"
                                 alt="<?= $this->escape($item->name); ?>">
                        <? else: ?>
                            <div class="b-no-photo b-video-item__no-foto"></div>
                        <? endif; ?>
                    </a>
                </div>


                <div class="b-video-item__right">
                <span class="b-video-item__date">
                    <?= date('d.m.Y', $item->date_cr); ?>
                </span>

                    <h2 class="b-video-item__title">
                        <a title="<?= $this->escape($item->name); ?>" class="b-link b-video-item__link_title"
                           href="<?= $item->link() ?>"><?= $item->name; ?></a>
                    </h2>

                    <div class="b-text b-video-item__preview">
                        <?= $item->getPreview(); ?>
                    </div>
                    <a class="b-link b-video-item__link_footer" href="<?= $item->link() ?>">
                        Подробнее ...
                    </a>
                </div>


            </div>
        <? endforeach ?>
        <?= $pagination->render() ?>
    <? else: ?>
        <h2>В данной момент нет видео</h2>
    <? endif; ?>
</div>
