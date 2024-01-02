<div class="b-filter-item b-filter-item_price">
    <div class="b-filter-item__title"><?= $filter->name ?></div>
    <div class="b-filter-item__layout">

        <div class="b-price-scale"
             data-min="<?= (int)$min ?>"
             data-max="<?= (int)$max ?>"
             data-values="[ <?= (int)$price_min ?>, <?= (int)$price_max ?> ]"
            >
            <div id="slider-price"></div>
        </div>

        <div class="b-filter-price__value">
            <div class="b-filter-price__with">
                <span id="slider-price-val1"><?= $price_min ?> p.</span>
            </div>
            <div class="b-filter-price__to">
                <span id="slider-price-val2"><?= $price_max ?> p.</span>
            </div>
        </div>

        <div class="b-price-scale__input">
            <input id="slider-price-input-min" disabled name="price_min" type="hidden" value="<?= (int)$price_min ?>">
            <input id="slider-price-input-max" disabled name="price_max" type="hidden" value="<?= (int)$price_max ?>">
        </div>

    </div>
</div>

<script type="text/javascript">

    $(function () {
        $('#slider-price').slider({
            range: true,
            min: <?= (int)$min?>,
            max: <?= (int)$max?>,
            values: [<?= (int)$price_min?>, <?= (int)$price_max?>],
            slide: function (event, ui) {

                var cmin = parseInt($('.b-price-scale').data('min'));
                var cmax = parseInt($('.b-price-scale').data('max'));
                var min = ui.values[0];
                var max = ui.values[1];

                if (cmin < min) {
                    $('#slider-price-input-min').removeAttr('disabled');
                } else {
                    $('#slider-price-input-min').attr('disabled', 'disabled');
                }

                if (cmax > max) {
                    $('#slider-price-input-max').removeAttr('disabled');
                } else {
                    $('#slider-price-input-max').attr('disabled', 'disabled');
                }

                $('#slider-price-val1').text(min + ' p.');
                $('#slider-price-val2').text(max + ' p.');
                $('#slider-price-input-min').val(min);
                $('#slider-price-input-max').val(max);
            }
        });
    });

</script>