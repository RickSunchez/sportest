<footer class="b-footer b-page__section b-page__section_footer " role="contentinfo">
    <div class="b-container">

        <div class="b-footer__menu">
            <?= $this->action('CMS:Core:Html:menu', array('code' => 'footer')); ?>
        </div>


        <div class="b-footer__address">
            <div class="b-copy">© 2015 Название компании</div>
            <div class="b-address">
                <div>Адрес: адрес Вашей компании</div>
                <div>Телефон: +7 (123) 456-78-90</div>
            </div>
        </div>


        <div class="b-footer__7thgroup">
            <div class="b-7thgroup">
                Cоздание и продвижение сайта - <a class="b-link" href="http://www.7thgroup.ru/">7thgroup</a>
            </div>
        </div>

    </div>
</footer><!-- .b-footer -->

<?= $this->partial('html/popup')?>


