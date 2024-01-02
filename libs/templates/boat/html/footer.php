<footer class="b-footer">

    <div class="l-container">


        <div class="b-footer__logo ">
            <div class="b-footer__tel">
                <div class="b-tel__label">Бесплатный звонок по России:</div>
                <div class="b-tel__num">8 800 350–27–25</div>
            </div>
            <div class="b-callback">
                <i class="fa fa-phone"></i>
                <a data-popup="#callback" href="javascript:;">Заказать звонок</a>
            </div>
            <div class="b-callback">
                <i class="fa fa-envelope"></i>
                <a data-popup="#letter" href="javascript:;">Написать сообщение</a>
            </div>
            <div class="b-footer__socials">
                <ul>
                    <li>
                        <a target="_blank" href="https://vk.com/sportest" class="social-icon vk" title="Мы Вконтакте"><i
                                class="social_icon fa fa-vk"></i></a>
                    </li>

                    <li>
                        <a target="_blank" href="https://www.youtube.com/channel/UCpZGvKt1zSxQTCEhWDOUcwg"
                           class="social-icon youtube"
                           title="Мы в Youtube"><i class="social_icon fa fa-youtube"></i></a>
                    </li>
                </ul>
            </div>
        </div>
        <div class="b-footer__deliver"></div>

        <div class="b-footer__contacts">

            <div class="b-contacts__title">Наши контакты:</div>


            <div class="b-table">
                <div class="b-table-cell">
                    <div class="b-contacts__shop">
                        <div class="b-name"><a data-href="<?= snippet('page',3)?>">Магазин:</a></div>
                        <div class="b-point">
                            <i class="fa fa-map-marker"></i>
                            Екатеринбург, ул. Гагарина 10
                        </div>
                        <div class="b-phone"><i class="fa fa-phone"></i>

                            +7 <span>(343)</span> 227-227-5
                        </div>
                    </div>

                </div>
                <div class="b-table-cell">
                    <div class="b-contacts__shop">
                        <div class="b-name"><a  data-href="<?= snippet('page',5)?>">Сервисный центр / склад:</a></div>
                        <div class="b-point">
                            <i class="fa fa-map-marker"></i>
                            Екатеринбург, Первомайская, 71 Б, литер А
                        </div>
                        <div class="b-phone"><i class="fa fa-phone"></i>
                            +7 <span>(343)</span> 204-97-77
                        </div>

                    </div>
                </div>
            </div>

        </div>


    </div>

    <div class="b-footer__line">
        <div class="l-container">
            <div class="b-footer__copy">
                © 2007 - <?= date('Y') ?> SportEst.ru — специализированный интернет-магазин лодок, моторов и спортивного
                снаряжения.
            </div>
            <div class="b-footer__developer">
                <a href="http://www.7thgroup.ru/">Разработка сайта – Seven Group</a>
            </div>
        </div>
    </div>
</footer>

<?= $this->partial('html/last') ?>

