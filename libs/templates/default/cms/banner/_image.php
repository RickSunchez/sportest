<div id="code_<?= $banner->pk() ?>" class="b-banner b-banner_image">

    <? if ($banner->url): ?>
        <a class="b-link" rel="nofollow" target="_blank" href="<?= $banner->link() ?>">
    <? endif; ?>
        <img class="b-image b-banner_image" width="<?= $banner->width ?>" height="<?= $banner->height ?>" src="<?= $banner->path ?>" alt=""/>
    <? if ($banner->url): ?>
        </a>
    <? endif ?>

</div>