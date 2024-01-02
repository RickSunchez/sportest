<? if ($image->loaded()): ?>
    <span class="meta" itemprop="image" itemscope itemtype="http://schema.org/ImageObject">
            <meta itemprop="url" content="<?= CMS\Core\Helper\Helpers::canonicalUrl($image->normal); ?>"/>
            <meta itemprop="height" content="<?= $image->height; ?>"/>
            <meta itemprop="width" content="<?= $image->width; ?>"/>
    </span>
    <meta itemprop="thumbnailUrl" content="<?= CMS\Core\Helper\Helpers::canonicalUrl($image->preview); ?>"/>
<? else: ?>
    <span itemprop="logo" itemscope itemtype="http://schema.org/ImageObject">
            <meta itemprop="url"
                  content="<?= CMS\Core\Helper\Helpers::canonicalUrl('/source/images/boat/logo_300.png'); ?>"/>
            <meta itemprop="height" content="300"/>
            <meta itemprop="width" content="47"/>
    </span>
    <meta itemprop="thumbnailUrl"
          content="<?= CMS\Core\Helper\Helpers::canonicalUrl('/source/images/boat/logo_300.png'); ?>"/>
<? endif; ?>