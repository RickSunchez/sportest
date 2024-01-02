var __click = schema_product = null;
var first_product, first_category = false;
$(function () {

    $(document).mouseleave(function (e) {
        var alert_popup = df.getCookie('alert_popup');
        if (e.clientY < 0 && !alert_popup) {
            $.magnificPopup.open({
                items: {
                    src: '#callback',
                    type: 'inline'
                },
                midClick: true,
                callbacks: {
                    open: function () {
                        df.setCookie('alert_popup', true, 1);
                    }
                }
            });
        }
    });

    var _autoComplete = new autoComplete({
        selector: 'input.b-search__input',
        minChars: 3,
        cache: true,
        source: function (term, response) {
            try {
                xhr.abort();
            } catch (e) {
            }
            xhr = $.post('[link:shop_search_data]', {term: term}, function (data) {
                first_product = first_category = false;
                response(data);
            });
        },
        renderItem: function (item, search) {
            search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
            var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
            var html = '';
            if (item['type'] == 'product') {
                if (!first_product) {
                    html = '<div class="autocomplete-suggestion autocomplete-suggestion__label" data-label="true" ><span class="type">Товары:</span></div>';
                    first_product = true;
                }
                html += '<div class="autocomplete-suggestion" data-name="' + item['name'] + '" data-link="' + item['link'] + '" data-val="' + search + '">' + item['name'].replace(re, "<b>$1</b>") + ' <div class="price">' + item['price'] + '</div></div>';
            } else {
                if (!first_category) {
                    html = '<div class="autocomplete-suggestion autocomplete-suggestion__label"  data-label="true" ><span class="type">Категории:</span></div>';
                    first_category = true;
                }
                html += '<div class="autocomplete-suggestion" data-name="' + item['name'] + '" data-link="' + item['link'] + '" data-val="' + search + '">' + item['name'].replace(re, "<b>$1</b>") + '</div>';
            }

            return html;
        },
        onSelect: function (e, term, item) {
            var is_label = item.getAttribute('data-label');
            if (is_label) {
                return false;
            }
            var name = item.getAttribute('data-name');
            $('.b-form-search__input').val(name);
            var link = item.getAttribute('data-link');
            window.location.href = link;
        }
    });


    $(".js-phone-mask").mask("+7 (999) 999-99-99");

    $('[data-href]').click(function () {
        $href = $(this).data('href');
        window.location.href = $href;
    });

    $('[data-image]').each(function () {
        var image = $(this).data('image');
        if (image) {
            $(this).css('background-image', 'url(' + image + ')').addClass('image--set');
        }
    });

    df.delegate('.js-image', 'click', function (e) {
        e.preventDefault();
        var $a = $(this);
        var $href = $a.attr('href');

        if ($href) {

            $.magnificPopup.open({
                items: {
                    src: $href,
                    type: 'image'
                },
                image: {
                    titleSrc: 'title'
                }
            });
        }
    });

    $('[data-click]').click(function () {
        __click = $(this).data('click');
    });

    $('[data-popup]').click(function () {
        $id = $(this).data('popup');
        if ($(this).data('product')) {
            schema_product = $(this).data('product');
        } else {
            schema_product = null;
        }
        $.magnificPopup.open({
            items: {
                src: $id,
                type: 'inline'
            },
            midClick: true
        });
    });

    $('.js-gallery').each(function () { // the containers for all your galleries
        $(this).magnificPopup({
            delegate: 'a', // the selector for gallery item
            type: 'image',
            gallery: {
                enabled: true
            },
            image: {
                titleSrc: 'title'
            },
            callbacks: {
                buildControls: function () {
                    if (this.arrowLeft)
                        this.contentContainer.append(this.arrowLeft.add(this.arrowRight));
                }
            }
        });
    });

    $(".b-search").hover(
        function () {
        }, function () {
            $('.b-category-btn.active').removeClass('active');
            $('.b-menu-sub.active').removeClass('active');
        }
    );

    $('.b-category-btn').click(function () {
        if ($(this).hasClass('active')) {
            $('.b-category-btn').removeClass('active');
            $('.b-menu-sub').removeClass('active');
        } else {
            $('.b-category-btn').addClass('active');
            $('.b-menu-sub').addClass('active');
        }
    });

    $('.b-menu-sub__item-yet-link').click(function (e) {
        e.preventDefault();
        $(this).parent().addClass('active');
    })

    $('.b-sections__nav a').click(function (e) {
        e.preventDefault();
        $id = $(this).attr('href');
        $('.b-sections__nav a').removeClass('active');
        $(this).addClass('active');
        $('.b-section__item').removeClass('active');
        $($id).addClass('active');
    });

    if ($('.b-product__carousel').length) {
        $('.b-product__carousel').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 1,
            slidesToScroll: 1,
            arrows: false,
            fade: true,
            asNavFor: '.b-product__carousel-nav'
        });
        $('.b-product__carousel-nav').slick({
            lazyLoad: 'ondemand',
            slidesToShow: 3,
            slidesToScroll: 1,
            asNavFor: '.b-product__carousel',
            arrows: false,
            focusOnSelect: true
        });
    }

    $('.b-info__title').click(function () {
        if ($(this).parent('.b-info').hasClass('b-info--active')) {
            $(this).parent('.b-info').removeClass('b-info--active');
            $(this).parent('.b-info').find('.b-info__text').slideUp(300);
        } else {
            $(this).parent('.b-info').addClass('b-info--active');
            $(this).parent('.b-info').find('.b-info__text').slideDown(300);
        }
    });


    $().UItoTop({easingType: 'easeOutQuart'});

    $('.b-goods__item,.b-products__item').hover(
        function () {
            $(this).addClass('hover')
        },
        function () {
            $(this).removeClass('hover')
        }
    );

    $("img.lazy").lazyload({
        effect: "fadeIn",
        placeholder: "/source/images/zero.gif"
    });


    $('.js-scroll').on('click', function (e) {
        e.preventDefault();

        var target = this.hash,
            $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top - 100
        });
    });

    $('.b-category__item-link').click(function (e) {
        e.preventDefault();
        $(document).off("scroll");

        var target = this.hash,
            $target = $(target);

        $('html, body').stop().animate({
            'scrollTop': $target.offset().top - 20,
        }, 500, function () {
            $target.find('.b-subcategories__title')
                .fadeIn('fast');
        });
    });

    $('.b-filters').submit(function (e) {
        e.preventDefault();
        var $form = $(this);
        var data = {};

        data.get = $form.serialize();
        data.cid = $form.data('cid');
        data.col_cid = $form.data('colCid');
        data.url = $form.attr('action');
        data.no_city = 1;

        df.shop.filters(data);
    });

    $('.js-hover').hover(
        function () {
            $(this).addClass('hover')
        },
        function () {
            $(this).removeClass('hover')
        }
    );


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

function update_cart() {
    $('.js-cart-update').click();
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


function calc_product($goodsId) {
    var product_data = {};
    var required = ['goods_id', 'article', 'image', 'price', 'price_raw'];

    //console.log('#product_' + $goodsId);
    //console.log($('#product_' + $goodsId));


    $amount = $('#product_' + $goodsId).find('.js-quantity').val();

    var options = calc_options();
    var option_data = {goods_id: $goodsId, amount: $amount, options: options, required: required, image: true};

    product_data['goods'] = option_data;
    product_data['additions'] = {};

    return product_data;
}


function calc_options() {
    var options = {};

    $('.b-select-option.active').each(function () {
        $variantId = $(this).data('variantId');
        $optionId = $(this).data('optionId');
        options[$optionId] = $variantId;
    });

    return options;
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