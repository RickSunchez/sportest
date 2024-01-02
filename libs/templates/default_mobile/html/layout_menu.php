<?= $this->partial('html/loading') ?>
<nav class="m-layout__menu-wrap" role="navigation">
    <div class="m-layout__menu">
        <!--Menu-->

        <?= $this->action('CMS:Core:Html:menu', array('code' => 'mobile')) ?>


    </div>
</nav>