<!--[if lt IE 9]>
<script src="//cdnjs.cloudflare.com/ajax/libs/html5shiv/r29/html5.min.js"></script>
<![endif]-->


<?DI()->getService('header')->render(false);?>
<?DI()->getService('header')->renderCss();?>
<script src="https://ajax.googleapis.com/ajax/libs/jquery/1.12.4/jquery.min.js"></script>
<?= $this->action('CMS:Core:Html:code',array('header'=>true)) ?>


