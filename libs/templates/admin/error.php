<!DOCTYPE html>
<html ng-app="df.admin" >
<head>
    <?= $this->partial('html/head');?>
</head>
<body ng-controller="AdminCtrl">
<?= $this->partial('html/loading')?>
<div id="wrap">
    <div id="content" class="container">
        <?= $response;?>
    </div>

    <br />
    <div id="push"></div>
</div>
<?= $this->partial('html/footer');?>
</body>
</html>