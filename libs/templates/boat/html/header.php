<header class="b-header" role="presentation">
    <div class="l-container">

        <div class="b-header__logo b-logo" data-href="<?= homepage(); ?>">
            <img class="b-logo__img" src="/source/images/boat/logo_300.png"
                 alt="Лодки, моторы, спортивное оборудование - магазин Спорт Есть">

            <div class="b-logo__text">Лодки, моторы, сервис, запчасти</div>
        </div>
        <div class="b-header__time_work">
            <? if ($time_work = city_builder()->getAttr('time_work')): ?>
                <div class="b-time_work__label">Время работы:</div>
                <div class="b-time_work">ПН-ПТ: <?= $time_work ?>
                </div>
                <? if ($time_work2 = city_builder()->getAttr('time_work2')): ?>
                    <div class="b-time_work">СБ: <?= $time_work2 ?></div>
                <? endif; ?>
                <? if ($time_work3 = city_builder()->getAttr('time_work3')): ?>
                    <div class="b-time_work">ВС: <?= $time_work3 ?></div>
                <? endif; ?>
            <? endif; ?>
        </div>
        <div class="b-header__contacts_region">
            <? if ($phone = city_builder()->getAttr('phone')): ?>
                <div class="b-phone">
                    <div class="b-phone__label">Звонки по <?= city_builder()->getName4() ?></div>
                    <div class="b-phone__num"><?= $phone; ?></div>
                    <? if ($wt = city_builder()->getAttr('wt')): ?>
                        <? $wt_raw = city_builder()->getAttr('wt_raw') ?>
                        <div class="b-phone-wt">
                            <a title="Написать в Whatsapp" target="_blank" href="<?= $wt_raw ?>">
                                <?= $wt ?>
                                <i class="fa fa-whatsapp"></i>
                            </a>
                        </div>
                    <? endif; ?>
                    <div class="b-time_work"><?= city_builder()->getAttr('street') ?>&nbsp;</div>
                </div>
            <? endif; ?>
        </div>
        <div class="b-header__contacts">

            <div class="b-phone">
                <div class="b-phone__label">Бесплатный для других регионов России</div>
                <div class="b-phone__num">8 800 350–27–25</div>
                <div class="b-email"><a href="mailto:info@sportest.ru">info@sportest.ru</a></div>
            </div>


        </div>

        <div class="b-header__basket">
            <?= $this->action('Shop:Store:Cart:cartMini'); ?>
        </div>
    </div>
</header>
<div class="l-container" style="text-align: right;margin-bottom: 3px;">
    <noindex><strong style="font-size: 18px; font-weight: 700;color: red;">
            Запчасти для лодочных моторов продаются только на Первомайской 71Б литер А
        </strong></noindex>
</div>

