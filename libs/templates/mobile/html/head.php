<!-- Option for HTA file
    <hta:application id=site.mobile
        applicationName=Mushroom
        showInTaskBar=yes
        caption=yes
        innerBorder=yes
        selection=no
        icon=favicon.ico
        sysMenu=yes
        windowState=normal
        scroll=no
        resize=no
        navigable=no
        contextmenu=yes />
        -->

<meta http-equiv="Content-Type" content="text/html; charset=utf-8">
<meta http-equiv="Cache-Control" content="no-cache">

<!-- Blackberry and etc. -->
<meta http-equiv="cleartype" content="on">
<meta name="HandheldFriendly" content="True">

<!-- IE -->
<meta http-equiv="X-UA-Compatible" content="IE=edge">
<!--[if IE]>
<meta http-equiv="imagetoolbar" content="no"/>
<meta http-equiv="MSThemeCompatible" content="no"/>
<![endif]-->

<!-- iPhone -->
<meta name="apple-mobile-web-app-capable" content="yes">
<meta name="apple-mobile-web-app-status-bar-style" content="black-translucent">
<!--<link rel="apple-touch-startup-image" href="images/icons/iPhone/startup.png">-->
<!--<link rel="apple-touch-icon" href="images/icons/iPhone/touch-icon-iphone.png">-->
<!--<link rel="apple-touch-icon" sizes="72x72" href="images/icons/iPhone/touch-icon-ipad.png">-->
<!--<link rel="apple-touch-icon" sizes="114x114" href="images/icons/iPhone/touch-icon-iphone-retina.png">-->
<!--<link rel="apple-touch-icon" sizes="144x144" href="images/icons/iPhone/touch-icon-ipad-retina.png">-->

<?
$header = DI()->getService('header');
$header->setMetaTag('viewport',
    'width=device-width, height=device-height, initial-scale=1.0, user-scalable=no, maximum-scale=1.0');
?>

<!-- Windows 8 -->
<meta name="application-name" content="<?= $header->getTitleString() ?>">
<meta name="msapplication-tooltip" content="<?= $header->getDescription(); ?>">
<meta name="msapplication-window" content="width=400;height=300">
<meta name="msapplication-TileColor" content="#990000">
<!--<meta name="msapplication-TileImage" content="images/icons/Win8/custom_icon_144.png">-->
<!--<meta name="msapplication-square70x70logo" content="images/icons/Win8/custom_icon_70.png">-->
<!--<meta name="msapplication-square150x150logo" content="images/icons/Win8/custom_icon_150.png">-->
<!--<meta name="msapplication-square310x310logo" content="images/icons/Win8/custom_icon_310.png">-->
<!--<meta name="msapplication-wide310x150logo" content="images/icons/Win8/custom_icon_310x150.png">-->

<!-- Android and etc. -->
<meta name="format-detection" content="telephone=no">
<meta name="format-detection" content="address=no">


<!--[if lt IE 9]>
<script src="http://html5shiv.googlecode.com/svn/trunk/html5.js"></script>
<![endif]-->


<? $header->render(false); ?>
<? $header->renderCss(); ?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<script>
    var dataLayer = dataLayer || [];
    var __options = __options || {};
    function uuidv4() {
        return 'xxxxxxxx-xxxx-4xxx-yxxx-xxxxxxxxxxxx'.replace(/[xy]/g, function(c) {
            var r = Math.random() * 16 | 0, v = c == 'x' ? r : (r & 0x3 | 0x8);
            return v.toString(16);
        });
    }
</script>
<?= $this->action('CMS:Core:Html:code', array('header' => true)) ?>

