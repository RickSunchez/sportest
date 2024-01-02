<!DOCTYPE html>
<html
        data-ng-app="df.app"
        itemscope
        itemtype="http://schema.org/WebSite"
        prefix="og: http://ogp.me/ns#">
<head>
    <?= $this->partial('html/head'); ?>
</head>
<body>
<?= $this->partial('html/first') ?>
<main class="b-page" id="top">
    <?= $this->partial('html/nav') ?>
    <?= $this->partial('html/header') ?>
    <?= $this->partial('html/search') ?>
    <div class="l-container">
        <?= $this->action('CMS:Core:Html:breadcrumbs'); ?>
        <?= $response ?>
    </div>
</main>
<?= $this->partial('html/footer') ?>

</body>
</html>

