angular.module('df.app')
    .controller('CartMiniController',
    ['$scope', '$http', 'dfLoading',
        function ($scope, $http, dfLoading) {
            $scope.goods = [];
            $scope.count = 0;
            $scope.count_prefix = null;
            $scope.value = null;
            $scope.price = null;
            $scope.list = false;

            $scope.getTitle = function () {
                if ($scope.count == 0) {
                    return 'Корзина пуста';
                } else {
                    return 'Перейти в корзину';
                }
            }

            $scope.init = function (list) {
                $http.post('[[link:shop_cart_data?action=getCartMini]]', {list: list})
                    .success(function (data) {
                        $scope.update(data);
                    });
            }

            $scope.update = function (data) {
                $scope.goods = data.goods;
                $scope.value = data.value;
                $scope.price = data.price;
                $scope.count = data.count;
                $scope.count_prefix = data.count_prefix;
            }

            $scope.delete = function (item, list) {
                if (dfLoading.is_loading('deleteMini')) {
                    return;
                }
                dfLoading.loading('deleteMini');
                $http.post('[[link:shop_cart_goods?action=deleteMini]]', {cartId: item.combination_hash, list: list})
                    .success(function (response) {
                        dfLoading.ready('deleteMini');
                        $scope.update(response);
                    });
            };

        }])
    .controller('CartController',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.goods = [];
            $scope.images = [];
            $scope.delivery = [];
            $scope.cities = [];
            $scope.payment_method = [];
            $scope.basket = [];
            $scope.form = {};
            $scope.user = {};

            $scope.deliveryId = 0;
            $scope.cityId = 0;
            $scope.paymentMethodId = 0;
            $scope.pointId = 0;

            $scope.isNameChanged = 1;
            $scope.isPhoneChanged = 1;
            $scope.isEmailChanged = 1;
            $scope.isAddressChanged = 1;
            $scope.isDateChanged = 1;
            $scope.isCompanyChanged = 1;

            $scope.isNameValid = 0;
            $scope.isPhoneValid = 0;
            $scope.isEmailValid = 0;
            $scope.isAddressValid = 0;
            $scope.isDateValid = 0;
            $scope.isCompanyValid = 0;

            $scope.init = function () {
                dfLoading.loading('cart', 'b-loading-ajax_center');
                $http.post('[[link:shop_cart_data?action=getCart]]')
                    .success(function (data) {
                        $scope.cities = data.cities;
                        $scope.delivery = data.delivery;
                        $scope.payment_method = data.payment_method;
                        $scope.user = data.user;
                        $scope.update(data);
                        dfLoading.ready('cart');
                    });
            };

            $scope.form_login = {};

            $scope.login_cart = function () {
                dfLoading.loading();
                $scope.form_login.is_data = true;
                $http.post('[[link:user_login_data?action=auth]]', $scope.form_login)
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            $scope.user = response.user;
                        }
                    });
            }

            $scope.update = function (data) {
                $scope.goods = data.goods;
                $scope.images = data.images;
                $scope.basket = data.basket;

                if ($scope.basket.city.is_active) {
                    $scope.cityId = $scope.basket.city.id;

                }
                $scope.pointId = $scope.basket.config.point_id;
                $scope.deliveryId = $scope.basket.delivery.id;
                $scope.paymentMethodId = $scope.basket.payment.id;

            }

            $scope.change_amount = function (item, amount) {
                ajaxChangeAmount(item.combination_hash, amount);
            };

            $scope.plus = function (item) {
                ajaxChangeAmount(item.combination_hash, 'up');
                dataLayer.push({
                    "ecommerce": {
                        "add": {
                            "products": [
                                {
                                    "id": item.goods_id,
                                    "name": item.name,
                                    "price": item.price_raw,
                                    "brand": item.vendor,
                                    "category": item.categories_str,
                                    "quantity": 1
                                }
                            ]
                        }
                    }
                });
            }

            $scope.minus = function (item) {
                ajaxChangeAmount(item.combination_hash, 'down');
                dataLayer.push({
                    "ecommerce": {
                        "remove": {
                            "products": [
                                {
                                    "id": item.goods_id,
                                    "name": item.name,
                                    "price": item.price_raw,
                                    "brand": item.vendor,
                                    "category": item.categories_str,
                                    "quantity": 1
                                }
                            ]
                        }
                    }
                });
            }

            function ajaxChangeAmount(cartId, type) {
                dfLoading.loading();
                $http.post('[[link:shop_cart_data?action=changeParams]]', {cart_id: cartId, type: type})
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.update(response);
                    });
            }

            $scope.changeDelivery = function (deliveryId) {
                dfLoading.loading();
                $http.post('[[link:shop_cart_data?action=changeParams]]', {delivery_id: deliveryId})
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.update(response);
                    });
            }

            $scope.changePayment = function (paymentId) {
                dfLoading.loading();
                $http.post('[[link:shop_cart_data?action=changeParams]]', {payment_id: paymentId})
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.update(response);
                    });
            }

            $scope.changeCity = function (city_id) {
                dfLoading.loading();
                $http.post('[[link:shop_cart_data?action=changeParams]]', {city_id: city_id})
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.update(response);
                    });
            }

            $scope.changePoint = function (point_id) {
                dfLoading.loading();
                $http.post('[[link:shop_cart_data?action=changeParams]]', {point_id: point_id})
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.update(response);
                    });
            }


            $scope.clear = function () {
                dfLoading.loading();
                $http.post('[[link:shop_cart_goods?action=clear]]')
                    .success(function (response) {
                        dfLoading.ready();
                        window.location.href = '/';
                    });
            }

            $scope.delete = function (item) {
                dfLoading.loading();
                $http.post('[[link:shop_cart_goods?action=delete]]', {cartId: item.combination_hash})
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.update(response);
                    });
                dataLayer.push({
                    "ecommerce": {
                        "remove": {
                            "products": [
                                {
                                    "id": item.goods_id,
                                    "name": item.name,
                                    "price": item.price_raw,
                                    "brand": item.vendor,
                                    "category": item.categories_str,
                                    "quantity": item.quantity
                                }
                            ]
                        }
                    }
                });
            };

            $scope.is_cod = function () {
                if ($scope.deliveryId == 3) {
                    return true;
                }
                return false;
            }

            $scope.get_delivery_value = function () {
                if ($scope.basket.delivery.value == 0) {
                    return 'бесплатно';
                } else {
                    return $scope.basket.delivery.price;
                }
            }

            $scope.is_pay_online  = function () {

              var _is_ = true;

                $.each($scope.goods, function (index, item) {
                    if(item.is_amount == 0){
                        return _is_ = false;
                    }
                });

                return _is_;
            }

            $scope.isSend = false;
            $scope.isError = false;
            $scope.send = function () {

                if (!$scope.deliveryId) {
                    df.log('error delivery');
                    dfNotice.error('Выберите способ доставки');
                    return;
                }

                if (!$scope.paymentMethodId) {
                    df.log('error  payment');
                    dfNotice.error('Выберите способ оплаты');
                    return;
                }

                if ($scope.form.name == null) {
                    df.log('error name');
                    $scope.isNameChanged = 0;
                    $scope.isNameValid = 0;
                    $scope.isError = true;
                }

                if ($scope.form.phone == null) {
                    df.log('error phone');
                    $scope.isPhoneChanged = 0;
                    $scope.isPhoneValid = 0;
                    $scope.isError = true;
                }

                if ($scope.form.email == null) {
                    df.log('error email');
                    $scope.isEmailChanged = 0;
                    $scope.isEmailValid = 0;
                    $scope.isError = true;
                }

                if ($scope.form.address == null && $scope.deliveryId != 1) {
                    df.log('error address');
                    $scope.isAddressChanged = 0;
                    $scope.isAddressValid = 0;
                    $scope.isError = true;
                }

                if ($scope.deliveryId == 1) {
                    $scope.form.address = null;
                }

                if ($scope.form.details == null && $scope.paymentMethodId == 4) {
                    df.log('error company');
                    $scope.isCompanyChanged = 0;
                    $scope.isCompanyValid = 0;
                    $scope.isError = true;
                }

                if ($scope.paymentMethodId != 4) {
                    $scope.form.details = null
                }

                if ($scope.paymentMethodId == 3 && !$scope.is_cod()) {
                    df.log('error наложный платеж');
                    dfNotice.error('Выберите способ оплаты');
                    return;
                }


                if ($scope.paymentMethodId == 1 && !$scope.is_pay_online()) {
                    df.log('error online pay ');
                    dfNotice.error('При товаре "ПОД ЗАКАЗ" онлайн оплата не возможна');
                    return;
                }

                if ($scope.isError) {
                    $scope.isError = false;
                    dfNotice.error('Заполните все поля');
                    df.log('error');
                    return;
                }

                if ($scope.isSend) {
                    df.log('isSend');
                    return;
                }

                dfLoading.loading();
                $scope.isSend = true;
                $http.post('[[link:shop_order_data?action=checkout]]', {
                    form: $scope.form,
                    pos: {name: 1, phone: 2, email: 3, address: 4, details: 5, comment: 6}
                })
                    .success(function (response) {
                        $scope.isSend = false;
                        $scope.isError = false;
                        dfLoading.ready();
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {

                            var products = [];
                            $.each($scope.goods, function (index, item) {
                                products.push({
                                    'id': item.goods_id,
                                    'name': item.name,
                                    'category': item.categories_str,
                                    'brand': item.vendor,
                                    'price': item.price_raw,
                                    'quantity': item.quantity
                                });
                            });

                            dataLayer.push({
                                "ecommerce": {
                                    "purchase": {
                                        "actionField": {
                                            "id": response.order.number,
                                            "goal_id": "40506379",
                                            "revenue": response.order.price_raw
                                        },
                                        "products": products
                                    }
                                }
                            });

                            window.location.href = response.link;
                        }
                    });
            }


        }])
    .controller('OneClickController',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};
            $scope.product_data = {};

            $scope.init = function (product_data) {
                $scope.product_data = product_data;
                $(".js-phone-mask_one").mask("+7 (999) 999-99-99");
            }

            $scope.send = function () {

                if ($scope.form.phone == null && $scope.form.email == null) {
                    dfNotice.error('Укажите Ваш контакт');
                    return;
                }

                if (dfLoading.is_loading('oneClick')) {
                    return;
                }

                dfLoading.loading('oneClick');

                $http.post('[[link:shop_cart_data?action=callback]]', {
                    product_data: $scope.product_data,
                    form: $scope.form
                })
                    .success(function (response) {
                        dfLoading.ready('oneClick');
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {
                            $scope.form = {};
                            $.magnificPopup.close();
                            dfNotice.ok(response.ok);
                        }
                    });


            }

        }])
    .controller('SearchController',
    ['$scope', '$http', 'dfLoading', 'dfNotice', '$sce',
        function ($scope, $http, dfLoading, dfNotice, $sce) {
            $scope.term = null;
            $scope.items = [];


            $scope.search = function () {

                if ($scope.term.length >= 3) {

                    $http.post('[link:shop_search_data]', {term: $scope.term})
                        .success(function (response) {
                            if (response)
                                $scope.items = response;
                        });

                }
            }

            $scope.getName = function (name) {
                var search = $scope.term;
                search = search.replace(/[-\/\\^$*+?.()|[\]{}]/g, '\\$&');
                var re = new RegExp("(" + search.split(' ').join('|') + ")", "gi");
                name = name.replace(re, "<b>$1</b>")
                name = $sce.trustAsHtml(name);
                return name;
            }

            $scope.getPrice = function (price) {
                if (!price) {
                    return '';
                }
                price = $sce.trustAsHtml(price);
                return price;
            }


            $scope.send = function () {
                df.generatorHideForm('[link:goods_search]', 'post', {query: $scope.term});
            }

            $scope.link = function (href) {
                window.location.href = href;
            }


        }]);

