<!DOCTYPE html>
<html ng-app="df.app" >
<head>
    <?= $this->partial('html/head');?>
</head>
<body ng-controller="CabinetCtrl">
<?= $this->partial('html/loading')?>
<div id="wrap">
    <?= $this->partial('html/header');?>
    <div class="container">
        <div class="row">
            <div class="col-md-3">
                <?= $this->action('CMS:Cabinet:Html:menu');?>
            </div>
            <div class="col-md-9">
                <?= $this->action('CMS:Cabinet:Html:breadcrumbs'); ?>
                <no_parser>
                    <?= $response; ?>
                </no_parser>
            </div>
        </div>
    </div>
</div>
<?= $this->partial('html/footer');?>
</body>
</html>