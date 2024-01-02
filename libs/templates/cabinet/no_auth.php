<!DOCTYPE html>
<html ng-app="df.app">
<head>
    <?= $this->partial('html/head'); ?>
</head>
<body ng-controller="CabinetCtrl">
<?= $this->partial('html/loading')?>

<div id="wrap">
    <div id="content" class="container">
        <div class="row" >
            <br /><br /><br /><br />
            <div class="col-md-offset-3 col-md-5">
                <div><?= $this->action('CMS:Cabinet:Html:breadcrumbs'); ?></div>
            </div>
        </div>
        <?= $response; ?>
    </div>

    <br/>

    <div id="push"></div>
</div>
<?= $this->partial('html/footer'); ?>
</body>
</html>