var __click = schema_product = null;
var first_product, first_category = false;
$(function () {


    if ($('.b-product__image').length) {
        $('.b-product__image').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            dots: true
        });
    }
    if ($('.b-like-product__list').length) {
        $('.b-like-product__list').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 2,
            slidesToScroll: 2,
            dots: true
        });
    }


    if ($('.b-subcategories-list').length) {
        $('.b-subcategories-list').slick({
            slidesToShow: 2,
            slidesToScroll: 2,
            arrows: false,
            dots: true
        });
    }

    $("img.lazy").lazyload({
        effect: "fadeIn",
        placeholder: "/source/images/zero.gif"
    });


    if ($('.js-carousel').length) {

        $('.js-carousel').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: false,
            arrows: false
        });

        $(window).resize(function () {
            $('.js-carousel').slick('resize');
        });

        $(window).on('orientationchange', function () {
            $('.js-carousel').slick('resize');
        });
    }

    if ($('.js-carousel2').length) {

        $('.js-carousel2').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 1,
            slidesToScroll: 1,
            dots: true,
            arrows: false
        });

        $(window).resize(function () {
            $('.js-carousel2').slick('resize');
        });

        $(window).on('orientationchange', function () {
            $('.js-carousel2').slick('resize');
        });
    }

});

function open_text(selected) {
    if ($(selected).hasClass('hide')) {
        $(selected).removeClass('hide');
    } else {
        $(selected).addClass('hide');
    }
}


function __reachGoal(click) {
    if (click)
        ym(1234599, "reachGoal", click);
}


function changed_product($goodsId) {
    var product_data = calc_product($goodsId);
    df.shop.changedProductOptions(product_data, 'b-loading-ajax_status_loading_product',
        function (response) {
            $('.init-price')
                .fadeOut('fast')
                .html(response.price.goods.all)
                .fadeIn('fast');
        });
}

function add_cart($goodsId) {
    var product_data = calc_product($goodsId);
    df.shop.addProductCart(product_data, 'b-loading-ajax_status_loading_product', null, update_cart);

}

function one_click($goodsId) {
    var product_data = calc_product($goodsId);
    df.ajaxPopup('[[link:shop_cart_data?action=callbackForm]]', {product_data: product_data}, function () {
        $(".js-phone-mask").mask("+7 (999) 999-99-99");
    });
}

function update_cart() {
    $('.js-cart-update').click();
}

function calc_product($goodsId) {
    var product_data = {};
    var required = ['goods_id', 'image', 'price', 'price_raw'];

    var options = calc_options($goodsId);
    var option_data = {goods_id: $goodsId, options: options, required: required, image: true};

    product_data['goods'] = option_data;
    product_data['additions'] = calc_additions($goodsId, required);

    return product_data;
}


function calc_options($goodsId) {
    var options = {};

    $('#product_' + $goodsId + ' .b-select-option').each(function () {
        $variantId = $(this).val();
        $optionId = $(this).data('optionId');
        options[$optionId] = $variantId;
    });

    return options;
}

function calc_additions($goodsId, required) {
    var additions = {};

    $('#product_' + $goodsId + ' .b-addition-item.active').each(function () {
        $productId = $(this).data('id');
        additions[$productId] = ({goods_id: $productId, options: [], required: required, image: true});
    });

    return additions;
}

function show_variant(variant_id) {
    df.ajaxPopup('[link:goods_option_data?action=info]', {variant_id: variant_id});
}


function credit_form1() {
    var chekPrice = $('#chekPrice').val();
    var firstPayment = $('#firstPayment').val();
    var firstPayment = Number(firstPayment);
    var chekPrice = Number(chekPrice);
    var chekprice2 = chekPrice;
    var chekPrice = chekprice2 - firstPayment;
    if (chekPrice > 300000 || chekPrice < 3000 || chekPrice == '') {
        alert("Сумма кредита должна быть от 3000 до 300'000 рублей");
        return false;
    }

    __options.order[0].price = chekPrice;

    window.location.href = 'https://my.pochtabank.ru/pos-credit?' + $.param(__options);
}

function credit_form2() {
    var chekPrice = $('#chekPrice').val();
    var termCredit = $('#termCredit').val();
    var firstPayment = $('#firstPayment').val();
    var chekprice2 = chekPrice;
    var chekPrice = chekprice2 - firstPayment;
    if (chekPrice > 300000 || chekPrice < 3000 || chekPrice == '') {
        alert("Сумма кредита должна быть от 3000 до 300'000 рублей");
        return false;
    }
    __options.order[0].price = chekPrice;
    __options.termCredit = termCredit;

    window.location.href = 'https://my.pochtabank.ru/pos-credit-v2?' + $.param(__options);
}