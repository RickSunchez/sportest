<? if (count($sliders)): ?>
    <section class="b-page__section b-page__section_slider">
        <div class="l-container">
            <div class="l-slider">
                <? $i = 0; ?>
                <? foreach ($sliders as $slider): ?>
                    <? if (isset($images[$slider->pk()])): ?>
                        <? $i++; ?>
                        <div class="l-slider__item"
                            <?= $slider->url ? 'data-href="' . $slider->url . '"' : '' ?> >
                            <img src="<?= $images[$slider->pk()]->normal ?>" alt="">
                        </div>
                    <? endif; ?>
                <? endforeach; ?>
            </div>
        </div>
        <script type="text/javascript">
            $(function () {
                $('.l-slider').slick({
                    dots: true,
                    arrows: false,
                    speed: 500,
                    slidesToShow: 1,
                    adaptiveHeight: true,
                    autoplay: true,
                    autoplaySpeed: 4000
                });
                $('.b-page__section_slider').show();
            });
        </script>

    </section>
<? endif; ?>
