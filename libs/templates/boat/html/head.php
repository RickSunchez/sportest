<!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/r29/html5.min.js"></script>
<script src="//css3-mediaqueries-js.googlecode.com/svn/trunk/css3-mediaqueries.js"></script>
<![endif]-->


<?DI()->getService('header')->render(false);?>
<?DI()->getService('header')->renderCss();?>
<meta name="viewport" content="width=device-width, initial-scale=1">
<script src="/source/js/plugin/jquery-1.11.0.min.js"></script>
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
<?= $this->action('CMS:Core:Html:code',array('header'=>true)) ?>



