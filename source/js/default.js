function AddBasketGoods(goods_id, min, max, step) {
    min = parseFloat(min.replace(",", "."));
    var stack_topleft = {"dir1": "down", "dir2": "right", "push": "top"};

    $input = $('#goods_' + goods_id + ' .js-quantity');
    var quantity = parseFloat($input.val().replace(",", "."));
    if (isNaN(quantity) || quantity < min) {
        quantity = min;
    }
    var $btn = $('#goods_' + goods_id + ' .js-cart__btn');

    if ($btn.hasClass('js-cart__btn_none')) {
        $.pnotify({
            text: 'Товара нет в наличии',
            title: 'Внимание',
            type: 'success',
            delay: 2000,
            closer: true,
            icon: 'glyphicon glyphicon-ok',
            addclass: "stack-topleft",
            stack: stack_topleft
        });
        return false;
    }

    $.ajax({
        url: '[link:shop_cart_goods?action=add]',
        type: 'POST',
        data: {id: goods_id, quantity: quantity},
        success: function (response) {
            if (!$btn.hasClass('js-cart__btn_in')) {
                $btn.addClass('js-cart__btn_in');
            }

            $.magnificPopup.open({
                items: {
                    src: response.html,
                    type: 'inline'
                }
            });


            $('.js-cart-click__update').click();

        },
        error: function () {
            alert("Ошибка сервера");
        }
    });


    return false;
}

$(function () {

    $('.js-image-popup').magnificPopup({
        type: 'image',
        image: {
            titleSrc: 'title'
        }
    });

    $('.js-gallery').each(function () { // the containers for all your galleries
        $(this).magnificPopup({
            delegate: 'a', // the selector for gallery item
            type: 'image',
            gallery: {
                enabled: true
            },
            callbacks: {
                buildControls: function () {
                    if (this.arrowLeft)
                        this.contentContainer.append(this.arrowLeft.add(this.arrowRight));
                }
            }
        });
    });

    $('.js-video').magnificPopup({
        type: 'iframe',

        iframe: {
            markup: '<div class="mfp-iframe-scaler">' +
            '<div class="mfp-close"></div>' +
            '<iframe class="mfp-iframe" frameborder="0" allowfullscreen></iframe>' +
            '</div>',
            patterns: {
                youtube: {
                    index: 'youtube.com/', // String that detects type of video (in this case YouTube). Simply via url.indexOf(index).

                    id: 'v=', // String that splits URL in a two parts, second part should be %id%
                    // Or null - full URL will be returned
                    // Or a function that should return %id%, for example:
                    // id: function(url) { return 'parsed id'; }

                    src: '//www.youtube.com/embed/%id%?autoplay=1' // URL that will be set as a source for iframe.
                },
                vimeo: {
                    index: 'vimeo.com/',
                    id: '/',
                    src: '//player.vimeo.com/video/%id%?autoplay=1'
                },
            },
            srcAction: 'iframe_src',
        }


    });

});



function one_click() {
    var product_data = calc_product();
    df.ajaxPopup('[link:shop_cart_data?action=callbackForm]', {product_data: product_data});
}


function changed_product() {
    var product_data = calc_product();
    df.shop.changedProductOptions(product_data, 'b-loading-ajax_status_loading_product',
        function (response) {
            $('.init-price')
                .fadeOut('fast')
                .html(response.price.goods.all)
                .fadeIn('fast');


            if (!df.hasObjectKey(response, 'additions')) {
                $('.b-navigation-product__menu').html('');
            } else {
                var html = '';
                var i = 1;
                $.each(response.additions, function (index, product) {

                    if (i == 4) {
                        html += '<div class="b-navigation-product__item">...</div>';
                        return false;
                    }

                    var src = '/source/images/no.png';
                    if (df.hasObjectKey(product, 'image')) {
                        src = product.image.preview;
                    }

                    html += '<div class="b-navigation-product__item">+' +
                        '<div class="b-navigation-product__item-image">' +
                        '<img src="' + src + '" alt="">' +
                        '</div>' +
                        '</div>';

                    i++;

                });

                $('.b-navigation-product__menu').html(html);
            }

        });
}

function calc_product() {
    var product_data = {};
    var required = ['goods_id', 'image', 'price', 'price_raw'];

    var options = {};
    $goodsId = $('.b-product').data('id');
    var option_data = {goods_id: $goodsId, options: options, required: required, image: true};

    product_data['goods'] = option_data;

    product_data['additions'] = {};
    $('.b-addition-item.active').each(function () {
        $goodsId = $(this).data('id');
        product_data['additions'][$goodsId] = ({goods_id: $goodsId, options: [], required: required, image: true});
    });

    return product_data;
}

function message(html) {
    $.magnificPopup.open({
        items: {
            src: '<aside id="callback_ok" class="b-popup b-popup_ok">' +
            '<div class="b-popup__text">' +
            html +
            '</div>' +
            '</aside>',
            type: 'inline'
        }
    });
}
