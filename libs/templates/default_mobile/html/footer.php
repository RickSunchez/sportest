<footer class="m-footer">
    <a class="m-footer__btn-top" href="#top">Наверх</a>

    <div class="m-footer__time">
        Звоните с 9:00 до 20:00
    </div>
    <div class="m-footer__phone">
        <a href="tel:<?= snippet('config', 'contacts.tel') ?>"><?= snippet('config', 'contacts.tel') ?></a>
    </div>
    <div class="m-footer__info">
        Доставка по всей России и СНГ
    </div>

    <div class="m-copy">
        © <?= date('Y') ?> Интернет-магазин бань бочек в <?= snippet('city', 'name', array('v' => 2)) ?>
    </div>


    <div class="m-code">
        <? DI()->getService('header')->renderJs(); ?>
        <?= $this->action('CMS:Core:Html:code') ?>

    </div>
</footer>