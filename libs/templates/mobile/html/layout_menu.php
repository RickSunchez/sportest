<nav class="m-layout__menu-wrap" role="navigation">
    <div class="m-layout__menu">

        <?= $this->action('Shop:Catalog:Shop:multiMenu'); ?>

        <?= $this->action('CMS:Core:Html:menu', array('code' => 'mobile')) ?>
    </div>
</nav>


<?= $this->action('CMS:Core:Html:code') ?>
<?= $this->partial('html/loading') ?>