<!--noindex-->
<div id="code_<?= $code; ?>" style="<?= $width ? 'width:' . $width . 'px;' : '' ?>" class="b-widget b-banners__list b-banners__list_<?= $code; ?>">
    <? foreach ($banners as $banner): ?>
        <? if ($banner->type_id == $banner::TYPE_IMAGE): ?>
            <?= $this->partial('cms/banner/_image', array('banner' => $banner,'code'=>$code,'width'=>$width)) ?>
        <? elseif ($banner->type_id == $banner::TYPE_HTML): ?>
            <?= $this->partial('cms/banner/_html', array('banner' => $banner,'code'=>$code,'width'=>$width)) ?>
        <?elseif ($banner->type_id == $banner::TYPE_FLASH): ?>
            <?= $this->partial('cms/banner/_flash', array('banner' => $banner,'code'=>$code,'width'=>$width)) ?>
        <?endif; ?>
    <? endforeach ?>
</div>
<!--/noindex-->
