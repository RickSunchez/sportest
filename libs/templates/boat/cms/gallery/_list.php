<? $rnd = \Delorius\Utils\Strings::random() ?>

<div class="b-gallery-big">
    <div class="b-gallery-for b-gallery-for_<?=$rnd?> js-gallery">
        <? foreach ($images as $key => $image): ?>
            <div class="item">
                <a href="<?= $image->normal ?>"><img data-lazy="<?= $image->normal ?>" alt="" /></a>
            </div>
        <? endforeach; ?>
    </div>
    <div class="b-gallery-nav b-gallery-nav_<?=$rnd?>">
        <? foreach ($images as $key => $image): ?>
            <div class="item">
                <div data-image="<?= $image->preview ?>" ></div>
            </div>
        <? endforeach; ?>
    </div>
</div>


<script>
    $(function () {

        $('.b-gallery-for_<?=$rnd?>').slick({
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.b-gallery-nav_<?=$rnd?>'
        });
        $('.b-gallery-nav_<?=$rnd?>').slick({
            slidesToShow: 10,
            slidesToScroll: 1,
            asNavFor: '.b-gallery-for_<?=$rnd?>',
            dots: false,
            focusOnSelect: true
        });

    });
</script>
