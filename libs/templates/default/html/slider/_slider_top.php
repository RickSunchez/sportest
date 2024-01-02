<? if (count($sliders)): ?>
    <div class="b-slider-top">
        <? foreach ($sliders as $slider): ?>
            <? if (isset($images[$slider->pk()])) ?>
                <div class="b-slider-top__item">
                <h2><?= $slider->title ?></h2>
            <img src="<?= $images[$slider->pk()]->normal ?>" alt=""/>
            </div>
        <? endforeach; ?>
    </div>
<? endif; ?>