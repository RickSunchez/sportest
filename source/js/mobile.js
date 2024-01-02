var __click, schema_product = null;
$(function () {

    $('[data-popup]').click(function () {
        $id = $(this).data('popup');
        window.__click = $a.data('click');

        $.magnificPopup.open({
            items: {
                src: $id,
                type: 'inline'
            },
            midClick: true
        });
    });


    $('[data-open]').click(function () {
        $id = $(this).data('open');
        if ($(this).data('product')) {
            schema_product = $(this).data('product');
        } else {
            schema_product = null;
        }
        $('[data-model="' + $id + '"]').addClass('js-model');
        $('.m-layout__main').addClass('js-model--active');
    });

    $('.js-model--close').click(function () {
        $('.js-model').removeClass('js-model');
        $('.js-model--active').removeClass('js-model--active');
    });

    $('.b-sections__name').click(function () {
        if ($(this).hasClass('b-sections__name--close')) {
            $(this).removeClass('b-sections__name--close')
        } else {
            $(this).addClass('b-sections__name--close')
        }
    });


    $('[data-href]').click(function () {
        $href = $(this).data('href');
        window.location.href = $href;
    });

    $(".js-phone-mask").mask("+7 (999) 999-99-99");

    $("img.lazy").lazyload({
        effect: "fadeIn",
        placeholder: "/source/images/zero.gif"
    });

    $('.b-info__title').click(function () {
        if ($(this).parent('.b-info').hasClass('b-info--active')) {
            $(this).parent('.b-info').removeClass('b-info--active');
            $(this).parent('.b-info').find('.b-info__text').slideUp(300);
        } else {
            $(this).parent('.b-info').addClass('b-info--active');
            $(this).parent('.b-info').find('.b-info__text').slideDown(300);
        }
    });


    $('.b-menu-horiz__link-select').click(function () {
        if ($(this).hasClass('active')) {
            $(this).removeClass('active');
        } else {
            $(this).addClass('active');
        }
        $('.b-menu-horiz__list').toggle();
    });

    $('[data-image]').each(function () {
        var image = $(this).data('image');
        if (image) {
            $(this).css('background-image', 'url(' + image + ')').addClass('image--set');
        }
    });

    $('[data-click]').click(function () {
        __click = $(this).data('click');
    });

    $('.b-info__name').click(function () {
        if ($(this).parent('.b-info').hasClass('b-info--active')) {
            $(this).parent('.b-info').removeClass('b-info--active');
            $(this).parent('.b-info').find('.b-info__text').slideUp(300);
        } else {
            $(this).parent('.b-info').addClass('b-info--active');
            $(this).parent('.b-info').find('.b-info__text').slideDown(300);
        }
    });

});

function __reachGoal(click) {
    if (click)
        ym(1234599, "reachGoal", click);
}