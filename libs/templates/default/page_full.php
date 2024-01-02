<!DOCTYPE html>
<html
    data-ng-app="df.app"
    itemscope
    itemtype="http://schema.org/WebSite"
    prefix="og: http://ogp.me/ns#" >
<head>
    <?= $this->partial('html/head'); ?>
</head>
<body class="b-page">

<?= $this->partial('html/loading')?>

<?= $this->partial('html/header')?>

<?= $this->partial('html/menu_top')?>

<?= $this->partial('html/content_c',array('response'=>$response));?>

<?= $this->partial('html/footer')?>

</body>
</html>