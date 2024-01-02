<!-- saved from url=(0014)about:internet -->
<!DOCTYPE html>
<html
    data-ng-app="df.app"
    lang="ru"
    itemscope
    itemtype="http://schema.org/WebSite"
    prefix="og: http://ogp.me/ns#"
    class="m-layout">
<head>
    <?= $this->partial('html/head'); ?>
</head>
<body class="m-layout__body">
<!-- menu v1 fixed -->

<?= $this->partial('html/layout_menu'); ?>

<main class="m-layout__main" id="top">
    <div class="m-layout__overlay"></div>
    <!-- menu v2 here init -->
    <?= $this->partial('html/menu', array('fixed' => false)); ?>

    <section class="m-content">
        <?= $this->action('CMS:Core:Html:breadcrumbs'); ?>
        <?= $response; ?>
    </section>

    <?= $this->partial('html/footer') ?>
</main>
</body>
</html>