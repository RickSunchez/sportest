if (typeof  df === 'undefined') {
    alert('need include df.js for df.shop.js');
}

if (typeof  jQuery === 'undefined') {
    alert('need include jQuery');
}

df.init_shop = true;
df.shop = {};

df.shop.changedProductOptions = function (product_data, loading_class, callback, complete) {

    if (df.is_loading('changedProductOptions')) {
        return;
    }

    if (callback == null) {
        alert('Укажите callback fn для изменений опций');
        return;
    }

    $.ajax({
        url: '[link:goods_option_data?action=changed]',
        type: "POST",
        data: {product_data: product_data},
        success: callback,
        beforeSend: function () {
            df.loading('changedProductOptions', loading_class);
        },
        complete: function () {
            df.ready('changedProductOptions');
            if (complete) {
                complete();
            }
        },
        error: function () {
            df.ready('changedProductOptions');
        },
        dataType: 'json'
    });

};

df.shop.btnAddCart = function (btn, class_in_cart, loading_class, callback, complete, complete2) {
    var $btn = $(btn);
    $goodsId = $btn.data('id');
    if ($btn.hasClass(class_in_cart)) {
        if (complete2) {
            complete2();
        }
        return;
    }
    $btn.addClass(class_in_cart);
    var product_data = {goods: {goods_id: $goodsId}};
    df.shop.addProductCart(product_data, loading_class, callback, complete);
};

df.shop.addProductCart = function (product_data, loading_class, callback, complete) {

    if (df.is_loading('addProductCart')) {
        return;
    }

    $.ajax({
        url: '[link:shop_cart_goods?action=add]',
        type: "POST",
        data: {product_data: product_data},
        success: callback || function (response) {
            df.ready('addProductCart');
            if (response.html) {
                $.magnificPopup.open({
                    items: {
                        src: response.html,
                        type: 'inline'
                    }
                });
            }
        },
        beforeSend: function () {
            df.loading('addProductCart', loading_class);
        },
        complete: function () {
            df.ready('addProductCart');
            if (complete) {
                complete();
            }
        },
        error: function () {
            df.ready('addProductCart');
        },
        dataType: 'json'
    });
};


df.shop.filters = function (data, callback) {

    if (df.is_loading('filters')) {
        return;
    }

    $.ajax({
        url: '[link:shop_filters_data]',
        type: "POST",
        data: data,
        dataType: 'json',
        success: callback || function (response) {
            df.ready('filters');
            if (response.url)
                window.location.href = response.url;
        },
        beforeSend: function () {
            df.loading('filters');
        },
        complete: function () {
            df.ready('filters');
        },
        error: function () {
            df.ready('filters');
        }
    });

}

df.shop.number_format = function (number, decimals, dec_point, thousands_sep) {

    var i, j, kw, kd, km;

    // input sanitation & defaults
    if (isNaN(decimals = Math.abs(decimals))) {
        decimals = 2;
    }
    if (dec_point == undefined) {
        dec_point = ",";
    }
    if (thousands_sep == undefined) {
        thousands_sep = ".";
    }

    i = parseInt(number = (+number || 0).toFixed(decimals)) + "";

    if ((j = i.length) > 3) {
        j = j % 3;
    } else {
        j = 0;
    }

    km = (j ? i.substr(0, j) + thousands_sep : "");
    kw = i.substr(j).replace(/(\d{3})(?=\d)/g, "$1" + thousands_sep);
    //kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).slice(2) : "");
    kd = (decimals ? dec_point + Math.abs(number - i).toFixed(decimals).replace(/-/, 0).slice(2) : "");

    return km + kw + kd;
};

df.shop.quantity__format = function (num) {
    var decimals = 3;
    if ((parseInt(num) - parseFloat(num)) == 0) {
        decimals = 0;
    }
    return df.shop.number_format(num, decimals, ',', '');
};

df.shop.change_quantity = function (sel, min, max) {
    if (!jQuery) {
        alert('need include jQuery');
    }
    min = parseFloat(min.replace(",", "."));
    max = parseFloat(max.replace(",", "."));
    $input = $(sel + ' .js-quantity');
    var num = parseFloat($input.val().replace(",", "."));
    var result = 0;
    var remainder = Math.floor(num % min);
    if (isNaN(num) || num <= min) {
        result = df.shop.quantity__format(min);
    } else if (!isNaN(max) && num >= max) {
        result = df.shop.quantity__format(max);
    } else {
        result = df.shop.quantity__format(num);
    }

    $input.val(result);
    return false;
};

df.shop.minus_quantity = function (sel, min, step) {
    min = parseFloat(min.replace(",", "."));
    step = parseFloat(step.replace(",", "."));
    if (step == 0) {
        step = 1;
    }
    $input = $(sel + ' .js-quantity');
    var num = parseFloat($input.val().replace(",", "."));
    var result = 0;
    if (isNaN(num) || (num - step) <= min) {
        result = df.shop.quantity__format(min);
    }
    else {
        result = df.shop.quantity__format(num - step);
    }
    $input.val(result);
    return false;
};

df.shop.plus_quantity = function (sel, min, max, step) {
    min = parseFloat(min.replace(",", "."));
    max = parseFloat(max.replace(",", "."));
    step = parseFloat(step.replace(",", "."));
    if (step == 0) {
        step = 1;
    }
    $input = $(sel + ' .js-quantity');
    var num = parseFloat($input.val().replace(",", "."));
    var result = 0;
    if (isNaN(num)) {
        result = df.shop.quantity__format(min);
    }
    else if (max == 0) {
        result = df.shop.quantity__format(num + step);
    }
    else if ((num + step) >= max) {
        result = df.shop.quantity__format(max);
    }
    else {
        result = df.shop.quantity__format(num + step);
    }
    $input.val(result);
    return false;
};


