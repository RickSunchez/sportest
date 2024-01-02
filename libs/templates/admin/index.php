<!DOCTYPE html>
<html ng-app="df.admin">
<head>
    <?= $this->partial('html/head'); ?>
</head>
<body ng-controller="AdminCtrl">
<?= $this->partial('html/loading') ?>
<div id="wrap">
    <?= $this->partial('html/header'); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-lg-2">
                <?= $this->action('CMS:Admin:Html:menu'); ?>
            </div>
            <div class="col-md-9 col-lg-8">
                <?= $this->action('CMS:Admin:Html:breadcrumbs'); ?>
                <?= $response; ?>
            </div>
        </div>
    </div>
    <br/>

    <div id="push"></div>
</div>
<?= $this->partial('html/footer'); ?>
</body>
</html>