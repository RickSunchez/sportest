<div id="code_<?= $banner->pk() ?>" class="b-banner b-banner_flash">

    <?if($banner->url):?>
    <a class="b-link b-banner__link_flash" rel="nofollow" href="<?= $banner->link()?>" target="_blank" style="display: block; position: absolute; z-index: 100; width: <?= $banner->width+20?>px;height: <?= $banner->height+20?>px;">
    </a>
    <?endif;?>
    <div style="position: relative; z-index: 1;">
        <object type="application/x-shockwave-flash"
                width="<?= $banner->width?>"
                height="<?= $banner->height?>"
                data="<?= $banner->path?>" >
            <param name="movie" value="<?= $banner->path?>"/>
            <param name="src" value="<?= $banner->path?>" />
            <param value="high" name="quality">
            <param value="opaque" name="wmode">
            <embed src="<?= $banner->path?>" quality="high"
                   pluginspage="http://www.adobe.com/shockwave/download/download.cgi?P1_Prod_Version=ShockwaveFlash"
                   type="application/x-shockwave-flash"
                   width="<?= $banner->width?>" height="<?= $banner->height?>" ></embed>
        </object>
    </div>

</div>