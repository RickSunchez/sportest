angular.module('df.admin')
    .controller('CategoriesListCtr',
        ['$scope', '$http', 'dfLoading', '$window', '$sce', 'dfImage',
            function ($scope, $http, dfLoading, $window, $sce, dfImage) {
                $scope.categories = [];
                $scope.counter = [];
                $scope.images = [];
                $scope.types = [];
                $scope.config_type = [];
                $scope.select_types = 1;
                $scope.query = '';
                $scope.show_categories = [];
                $scope.select_categories = [];

                $scope.init = function () {
                    $scope.select_types = $window._type_id;
                    $scope.config_type = $window._config_type;
                    $scope.counter = $window._counter;
                    $scope.types = $window._types;
                    $scope.select($window._type_id);
                }

                $scope.update = function () {

                    if (!confirm('Вы действительно хотите cинхронизировать каталог?')) {
                        return false;
                    }

                    if (dfLoading.is_loading('update')) {
                        return;
                    }
                    dfLoading.loading('update');
                    $http.post('[[link:admin_category_data?action=update]]')
                        .success(function (response) {
                            dfLoading.ready();
                            window.location.reload();
                        });
                }

                $scope.show_category = function (category) {
                    if (category.pid == 0) {
                        return true;
                    }
                    if ($scope.show_categories[category.pid]) {
                        return true;
                    }
                    return false;
                }

                $scope.show_category_child = function (category) {
                    if ($scope.show_categories[category.cid]) {
                        delete($scope.show_categories[category.cid]);
                        delete($scope.select_categories[category.cid]);
                    }
                    else {
                        $scope.show_categories[category.cid] = true;
                        if ($scope.has_child(category)) {
                            $scope.select_categories[category.cid] = true;
                        }
                    }
                }

                $scope.is_selected = function (category) {
                    if ($scope.select_categories[category.cid]) {
                        return true;
                    }
                    return false;
                }

                $scope.has_child = function (category) {
                    if ($scope.counter[category.cid] != 0) {
                        return true;
                    }
                    return false;
                }

                $scope.getCategories = function (pid) {
                    var categories = [];
                    $.each($scope.categories, function (index, cat) {
                        if (cat.pid == pid) {
                            categories.push(cat);
                        }
                    });
                    return categories;
                }

                $scope.getImageSrc = function (cid) {
                    if ($scope.select_types == 2)
                        return;
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == cid) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                }

                $scope.getLinkAddGoods = function (category) {
                    var html_code, url, name;
                    $.each($scope.config_type, function (index, config_type) {
                        if (config_type.id == $scope.select_types) {
                            url = config_type.add + '?cid=' + category.cid;
                            name = config_type.addName;
                            return true;
                        }
                    });
                    html_code = '<a href="' + url + '"><i class="glyphicon glyphicon-shopping-cart"></i> Добавить ' + name + '</a>';
                    return $sce.trustAsHtml(html_code);
                }

                $scope.getLinkGoods = function (category) {
                    var html_code, url, name;
                    $.each($scope.config_type, function (index, config_type) {
                        if (config_type.id == $scope.select_types) {
                            url = config_type.path + '?cid=' + category.cid;
                            name = config_type.name;
                            return true;
                        }
                    });
                    html_code = '[<a href="' + url + '">' + name + '</a>:' + category.goods + 'шт.]';
                    return $sce.trustAsHtml(html_code);
                }

                $scope.select = function (typeId) {
                    $scope.select_types = typeId;
                    dfLoading.loading();
                    $scope.categories = [];
                    $scope.images = [];
                    $http.post('[[link:admin_category_data?action=categories]]', {type_id: typeId})
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.images = response.images;
                            $scope.categories = response.categories;
                        });
                }

                $scope.show_select_type = function () {
                    var i = 0;
                    $.each($scope.types, function () {
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.up = function (cat) {
                    $scope.ajaxChangePos(cat.cid, cat.pos, 'up');
                }

                $scope.down = function (cat) {
                    $scope.ajaxChangePos(cat.cid, cat.pos, 'down');
                }

                $scope.change_pos = function (cat) {
                    $scope.ajaxChangePos(cat.cid, cat.pos, 'edit');
                }


                $scope.ajaxChangePos = function (id, pos, type) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_data?action=changePos]]', {id: id, pos: pos, type: type})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.categories = response.categories;
                                return;
                            }
                        });
                }


                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_category_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.categories, function (index, cat) {
                                    if (cat.cid == id) {
                                        $scope.categories.splice(index, 1);
                                        return false;
                                    }
                                });
                                alert(response.ok);
                                return;
                            }
                        });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.set(file, id,
                            '\\Shop\\Catalog\\Entity\\Category',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.image_id == data.image_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };


                $scope.editSelectCats = function () {
                    var action = $(".select_action").val();
                    if (action.length == 0) {
                        dfNotice.error('Выберите действие');
                        return;
                    }
                    var ids = [];
                    $('.catsIds').each(function (index, checkbox) {
                        if ($(checkbox).attr("checked")) {
                            ids.push($(checkbox).val());
                        }
                    });
                    if (ids.length == 0) {
                        dfNotice.error('Выберите товар');
                        return;
                    }

                    if (action == 'delete') {
                        if (!confirm('Вы действительно хотите удалить?')) {
                            return false;
                        }
                    }

                    if (action == 'edit') {
                        var url = '[link:admin_category?action=editIds]';
                        url += '?ids=' + ids + '&type_id=' + $scope.select_types;
                        $window.location.href = url;
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_category_data?action=selectEdit]', {
                        action: action,
                        ids: ids
                    })
                        .success(function (response) {
                            var url = '[link:admin_category?action=list]?type_id=' + $scope.select_types;
                            $window.location.href = url;
                        });

                }


            }])
    .controller('CategoryController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.category = {};
                $scope.categories = [];
                $scope.image = null;
                $scope.meta = {};
                $scope.meta_goods = {};
                $scope.ckeditor = [];
                $scope.filter_types = [];
                $scope.filter_goods_params = [];
                $scope.chara = [];
                $scope.filters = [];

                $scope.init = function () {
                    $scope.ckeditor['top'] = CKEDITOR.replace('text_top');
                    $scope.ckeditor['below'] = CKEDITOR.replace('text_below');
                    $scope.ckeditor['text_goods'] = CKEDITOR.replace('text_goods');

                    $scope.category = $window._category;
                    $scope.meta = $window._meta;
                    $scope.meta_goods = $window._meta_goods;
                    $scope.category.pid = $window._pid;
                    $scope.category.type_id = $window._type_id;
                    $scope.image = $window._image;
                    $scope.filter_types = $window._filter_types;
                    $scope.filter_goods_params = $window._filter_goods_params;
                    $scope.chara = $window._chara;

                    $.each($window._filters, function (index, filter) {
                        $scope.addFilter(filter);
                    });

                    $scope.loadProduct();
                    $scope.getCategories($window._type_id);
                    $scope.getQuery();
                    $scope.update();
                }

                $scope.getCategories = function (typeId) {
                    dfLoading.show('.category_load', 'getCategories');
                    $scope.categories = [];
                    $http.post('[link:admin_category_data?action=categoriesSelect]', {
                        type_id: typeId,
                        placeholder: 'Без категории'
                    })
                        .success(function (response) {
                            dfLoading.hide('.category_load', 'getCategories');
                            $scope.categories = response.categories;
                        });
                }

                $scope.update = function () {
                    setTimeout(function () {
                        $('.select_update').each(function () {
                            var value = $(this).data('value');
                            $("option[value='" + value + "']", this).attr("selected", "selected");
                        });
                    }, 150);
                }

                $scope.addFilter = function (filter) {
                    filter = filter || {type_id: 0};
                    var inc = $scope.filters.length;
                    filter.inc = inc;
                    filter.show = 1;
                    $scope.filters.push(filter);
                }

                $scope.deleteFilter = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.filters[inc].show = 0;
                    if ($scope.filters[inc].filter_id) {
                        $http.post('[[link:admin_category_data?action=deleteFilter]]', {
                            id: $scope.filters[inc].filter_id
                        });
                    }
                }
                $scope.selectFilter = function (inc) {
                    $scope.filters[inc].value = 0;
                }

                $scope.selectFilterValue = function (filter) {
                    setTimeout(function () {
                        $("#input_value_" + filter.inc + " option[value='" + filter.value + "']", this).attr("selected", "selected");
                    }, 100);
                };

                $scope.getValue = function (inc) {
                    var arr = [];
                    if ($scope.filters[inc].type_id == 1) {
                        $.each($scope.filter_goods_params, function (index, item) {
                            arr.push(item);
                        });
                        return arr;
                    }
                    if ($scope.filters[inc].type_id == 2) {
                        $.each($scope.chara, function (index, chara) {
                            arr.push(chara);
                        });
                        return arr;
                    }
                    return arr;
                }

                $scope.getQuery = function () {
                    if ($scope.category.cid) {
                        $scope.query = '?id=' + $scope.category.cid;
                    } else {
                        $scope.query = '?type=' + $scope.category.type_id;
                    }
                }

                $scope.save = function () {
                    $scope.category.text_top = $scope.ckeditor['top'].getData();
                    $scope.category.text_below = $scope.ckeditor['below'].getData();
                    if ($scope.ckeditor['text_goods'].getData()) {
                        $scope.meta_goods.text = $scope.ckeditor['text_goods'].getData()
                    }

                    var filters = [];
                    $.each($scope.filters, function (inc, filter) {
                        if ($scope.filters[inc].show == 1) {
                            filters.push(filter);
                        }
                    });

                    dfLoading.loading();
                    if ($scope.category.cid)
                        $http.post('[[link:admin_category_data?action=edit]]', {
                            category: $scope.category,
                            meta: $scope.meta,
                            meta_goods: $scope.meta_goods,
                            filters: filters
                        })
                            .success(function (response) {
                                dfLoading.ready();
                                if (response.errors) {
                                    dfNotice.errors(response.errors);
                                }

                                if (response.ok) {
                                    dfNotice.ok(response.ok);
                                    $scope.category = response.category;
                                    $scope.filters = [];
                                    $.each(response.filters, function (index, filter) {
                                        $scope.addFilter(filter);
                                    });

                                    $scope.update();

                                }
                            });
                    else
                        $http.post('[[link:admin_category_data?action=add]]', {
                            category: $scope.category,
                            meta: $scope.meta,
                            meta_goods: $scope.meta_goods,
                            filters: $scope.filters
                        })
                            .success(function (response) {
                                dfLoading.ready();

                                if (response.errors) {
                                    dfNotice.errors(response.errors);
                                    alert('Исправте ошибки');
                                }

                                if (response.ok) {
                                    var href = '[[link:admin_category?action=edit]]?id=' + response.id;
                                    window.location.href = href;
                                }
                            });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\Shop\\Catalog\\Entity\\Category',
                            function (data, status, headers, config) {
                                dfLoading.ready();
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    $scope.image = data;
                                }
                            });
                    }
                };


                /**
                 * Popular products
                 */

                $scope.products = [];
                $scope.items = [];

                $scope.$on('selectGoods', function (ev, goods) {
                    $scope.addProduct(goods);
                });


                $scope.getProduct = function (product_id) {
                    var product = {};
                    $.each($scope.products, function (index, item) {
                        if (item.goods_id == product_id) {
                            return product = item;
                        }
                    });

                    return product;
                }

                $scope.loadProduct = function () {
                    dfLoading.show('.product_load', 'getPopularGoods');
                    $http.post('[[link:admin_category_popular_data?action=load]]', {
                        cid: $scope.category.cid
                    })
                        .success(function (response) {
                            dfLoading.hide('.product_load', 'getPopularGoods');

                            $scope.items = response.items;
                            $scope.products = response.products;

                        });
                }


                $scope.addProduct = function (goods) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_popular_data?action=add]]', {
                        goods_id: goods.goods_id,
                        cat_id: $scope.category.cid,
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.loadProduct();
                            }
                        });
                }


                $scope.deleteProduct = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_category_popular_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.items, function (index, item) {
                                    if (item.id == id) {
                                        $scope.items.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                        });
                };

                $scope.saveProduct = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_popular_data?action=save]]', {
                        item: item
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.load();
                            }
                        });
                };

                /**
                 * Operation
                 */

                $scope.url_action_operation = '[link:admin_category_options_data?action=_action_]';

                $scope.operation_url = function (action) {
                    var url = $scope.url_action_operation;
                    return url.replace("_action_", action);
                }

                $scope.operation = function (action) {
                    dfLoading.loading();
                    $http.post($scope.operation_url(action), {
                        id: $scope.category.cid
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }

                $scope.operation_off_goods = function () {
                    if (!confirm('Вы действительно хотите отключить все товары этой категории?')) {
                        return false;
                    }
                    $scope.operation('offGoods');
                }

                $scope.operation_off_cats_goods = function () {
                    if (!confirm('Вы действительно хотите отключить все категории и товары этой категории?')) {
                        return false;
                    }
                    $scope.operation('offCatsGoods');
                }


                $scope.operation_del_goods = function () {
                    if (!confirm('Вы действительно хотите удалить все товары этой категории?')) {
                        return false;
                    }
                    $scope.operation('delGoods');
                }

                $scope.operation_del_cats_goods = function () {
                    if (!confirm('Вы действительно хотите удалить все категории и товары этой категории?')) {
                        return false;
                    }
                    $scope.operation('delCatsGoods');
                }


            }])
    .controller('CategoryIdsController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.category = {};
                $scope.categories = [];
                $scope.meta = {};
                $scope.ckeditor = [];
                $scope.filter_types = [];
                $scope.filter_goods_params = [];
                $scope.chara = [];
                $scope.filters = [];
                $scope.ids = [];
                $scope.type_id = 1;

                $scope.init = function () {
                    $scope.ckeditor['top'] = CKEDITOR.replace('text_top');
                    $scope.ckeditor['below'] = CKEDITOR.replace('text_below');
                    $scope.ids = $window._ids;

                    $scope.type_id = $window._type_id
                    $scope.filter_types = $window._filter_types;
                    $scope.filter_goods_params = $window._filter_goods_params;
                    $scope.chara = $window._chara;

                    $scope.getCategories($window._type_id);

                }

                $scope.getCategories = function (typeId) {
                    dfLoading.show('.category_load', 'getCategories');
                    $scope.categories = [];
                    $http.post('[link:admin_category_data?action=categoriesSelect]', {
                        type_id: typeId,
                        placeholder: 'Без категории'
                    })
                        .success(function (response) {
                            dfLoading.hide('.category_load', 'getCategories');
                            $scope.categories = response.categories;
                        });
                }


                $scope.addFilter = function (filter) {
                    filter = filter || {type_id: 0};
                    var inc = $scope.filters.length;
                    filter.inc = inc;
                    filter.show = 1;
                    $scope.filters.push(filter);
                }

                $scope.deleteFilter = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.filters[inc].show = 0;
                    if ($scope.filters[inc].filter_id) {
                        $http.post('[[link:admin_category_data?action=deleteFilter]]', {
                            id: $scope.filters[inc].filter_id
                        });
                    }
                }
                $scope.selectFilter = function (inc) {
                    $scope.filters[inc].value = 0;
                }

                $scope.selectFilterValue = function (filter) {
                    setTimeout(function () {
                        $("#input_value_" + filter.inc + " option[value='" + filter.value + "']", this).attr("selected", "selected");
                    }, 100);
                };

                $scope.getValue = function (inc) {
                    var arr = [];
                    if ($scope.filters[inc].type_id == 1) {
                        $.each($scope.filter_goods_params, function (index, item) {
                            arr.push(item);
                        });
                        return arr;
                    }
                    if ($scope.filters[inc].type_id == 2) {
                        $.each($scope.chara, function (index, chara) {
                            arr.push(chara);
                        });
                        return arr;
                    }
                    return arr;
                }

                $scope.save = function () {
                    $scope.category.text_top = $scope.ckeditor['top'].getData();
                    $scope.category.text_below = $scope.ckeditor['below'].getData();

                    var filters = [];
                    $.each($scope.filters, function (inc, filter) {
                        if ($scope.filters[inc].show == 1) {
                            filters.push(filter);
                        }
                    });

                    dfLoading.loading();

                    $http.post('[[link:admin_category_data?action=saveIds]]', {
                        category: $scope.category,
                        meta: $scope.meta,
                        filters: filters,
                        ids: $scope.ids
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.filters = [];
                            }
                        });

                };

            }])
    .controller('CategoryCollectionListController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.collections = [];
                $scope.images = [];
                $scope.init = function (cid) {
                    $scope.select(cid);
                };


                $scope.select = function (cid) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_collection_data?action=collections]]', {cid: cid})
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.images = response.images;
                            $scope.collections = response.collections;
                        });
                };


                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_collection_data?action=status]]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.collections, function (index, item) {
                                    if (item.id == id) {
                                        $scope.collections[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                };

                $scope.type = function (id, type) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_collection_data?action=type]]', {id: id, type: type})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.collections, function (index, item) {
                                    if (item.id == id) {
                                        $scope.collections[index].type = type;
                                        return;
                                    }
                                });
                            }
                        });
                };


                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_category_collection_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.collections, function (index, item) {
                                    if (item.id == id) {
                                        $scope.collections.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                        });
                };


                $scope.getImageSrc = function (target_id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == target_id) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                };


                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.set(file, id,
                            '\\Shop\\Catalog\\Entity\\Collection',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.target_id == data.target_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };

                $scope.change_pos = function (collection) {
                    $scope.ajaxChangePos(collection.id, collection.pos, 'edit');
                };

                $scope.ajaxChangePos = function (id, pos, type) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_collection_data?action=changePos]]', {
                        id: id,
                        pos: pos,
                        type: type
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }


            }])
    .controller('CategoryCollectionController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.collection = {};
                $scope.image = null;
                $scope.meta = null;
                $scope.ckeditor = [];
                $scope.filters = [];
                $scope.filter_types = [];
                $scope.filter_goods_params = [];
                $scope.chara = [];
                $scope.chara_list = [];
                $scope.chara_goods = [];
                $scope.chara_values = [];


                $scope.init = function (cid) {

                    $scope.ckeditor['top'] = CKEDITOR.replace('text_top');
                    $scope.ckeditor['below'] = CKEDITOR.replace('text_below');

                    $scope.collection = $window._collection;
                    $scope.image = $window._image;
                    $scope.meta = $window._meta;

                    $scope.filter_types = $window._filter_types;
                    $scope.filter_goods_params = $window._filter_goods_params;
                    $scope.chara = $window._chara;

                    $scope.chara_values = $window._chara_values;
                    $scope.chara_list = $window._chara_list;

                    $.each($window._filters, function (index, filter) {
                        $scope.addFilter(filter);
                    });

                    $.each($window._chara_goods, function (index, chara_goods) {
                        $scope.addChara(chara_goods);
                    });

                }


                $scope.addFilter = function (filter) {
                    filter = filter || {type_id: 0};
                    var inc = $scope.filters.length;
                    filter.inc = inc;
                    filter.show = 1;
                    $scope.filters.push(filter);
                }

                $scope.deleteFilter = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.filters[inc].show = 0;
                    if ($scope.filters[inc].filter_id) {
                        $http.post('[[link:admin_category_data?action=deleteFilter]]', {
                            id: $scope.filters[inc].filter_id
                        });
                    }
                }
                $scope.selectFilter = function (inc) {
                    $scope.filters[inc].value = 0;
                }

                $scope.selectFilterValue = function (filter) {
                    setTimeout(function () {
                        $("#input_value_" + filter.inc + " option[value='" + filter.value + "']", this).attr("selected", "selected");
                    }, 100);
                };

                $scope.getValue = function (inc) {
                    var arr = [];
                    if ($scope.filters[inc].type_id == 1) {
                        $.each($scope.filter_goods_params, function (index, item) {
                            arr.push(item);
                        });
                        return arr;
                    }
                    if ($scope.filters[inc].type_id == 2) {
                        $.each($scope.chara, function (index, chara) {
                            arr.push(chara);
                        });
                        return arr;
                    }
                    return arr;
                }


                $scope.addChara = function (chara_goods) {
                    chara_goods = chara_goods || {character_id: 0, value_id: ""};
                    var inc = $scope.chara_goods.length;
                    chara_goods.inc = inc;
                    $scope.chara_goods.push(chara_goods);
                }

                $scope.deleteChara = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.chara_goods[inc].delete = 1;
                }

                $scope.selectChara = function (inc) {
                    $scope.chara_goods[inc].value_id = '';
                }


                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\Shop\\Catalog\\Entity\\Collection',
                            function (data, status, headers, config) {
                                dfLoading.ready();
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    $scope.image = data;
                                }
                            });
                    }
                };

                $scope.save = function () {
                    $scope.collection.text_top = $scope.ckeditor['top'].getData();
                    $scope.collection.text_below = $scope.ckeditor['below'].getData();

                    var filters = [];
                    $.each($scope.filters, function (inc, filter) {
                        if ($scope.filters[inc].show == 1) {
                            filters.push(filter);
                        }
                    });

                    dfLoading.loading();

                    $http.post('[[link:admin_category_collection_data?action=save]]', {
                        collection: $scope.collection,
                        meta: $scope.meta,
                        filters: filters,
                        chara_goods: $scope.chara_goods

                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                            }

                            if (response.ok) {
                                var href = '[[link:admin_category_collection?action=edit]]?id=' + response.id;
                                window.location.href = href;
                            }
                        });

                };


            }])
    .controller('GoodsListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', '$sce', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, $sce, dfImage) {
                $scope.goods = {};
                $scope.goods_types = [];
                $scope.currency = [];
                $scope.type_id = null;
                $scope.categories = [];
                $scope.categories_label = 'loading...';
                $scope.get = {};
                $scope.images = [];
                $scope.html = '';

                $scope.show_select_type = function () {
                    var i = 0;
                    $.each($scope.goods_types, function () {
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.select_type = function (typeId, cid) {
                    $scope.type_id = typeId;
                    $scope.get.type_id = typeId;
                    $scope.get.cid = cid || 0;
                    $scope.categories = [];
                    dfLoading.show('.category_load');
                    $http.post('[[link:admin_category_data?action=categoriesSelect]]', {
                        type_id: typeId,
                        placeholder: 'Все категории'
                    })
                        .success(function (response) {
                            dfLoading.hide('.category_load');
                            $scope.categories = response.categories;
                        });

                }

                $scope.selectAll = function () {
                    if (!$('.goods_select_all').attr("checked")) {
                        $('.goods_ids').each(function (index, checkbox) {
                            $(checkbox).removeAttr("checked", "checked");
                        });
                    } else {
                        $('.goods_ids').each(function (index, checkbox) {
                            $(checkbox).attr("checked", "checked");
                        });
                    }
                }

                $scope.to_trusted = function (html_code) {
                    return $sce.trustAsHtml(html_code);
                }

                $scope.editSelectGoods = function () {
                    var action = $(".select_action").val();
                    if (action.length == 0) {
                        dfNotice.error('Выберите действие');
                        return;
                    }
                    var ids = [];
                    $('.goods_ids').each(function (index, checkbox) {
                        if ($(checkbox).attr("checked")) {
                            ids.push($(checkbox).val());
                        }
                    });
                    if (ids.length == 0) {
                        dfNotice.error('Выберите товар');
                        return;
                    }

                    if (action == 'edit') {
                        var url = '[link:admin_goods?action=editIds]';
                        url += '?ids=' + ids + '&type_id=' + $scope.type_id;
                        $window.location.href = url;
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_data?action=selectEdit]]', {
                        action: action,
                        ids: ids,
                        get: $scope.get
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.goods = response.goods;
                            $scope.images = response.images;
                            $('.goods_select_all').removeAttr("checked");
                        });

                }

                $scope.init = function () {
                    $scope.goods = $window._goods;
                    $scope.type_id = $window._type_id;
                    $scope.goods_types = $window._goods_types;
                    $scope.currency = $window._currency;
                    $scope.images = $window._images;
                    $scope.get = $window._get;
                    $scope.select_type($scope.type_id, $scope.get.cid);
                }

                $scope.saveGoods = function (goods) {
                    dfLoading.loading();

                    $http.post('[[link:admin_goods_data?action=save]]', {
                        goods: goods
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                return;
                            }
                        });
                }

                $scope.getImageSrc = function (goods_id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == goods_id) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                }

                $scope.change_pos = function (goods) {
                    $scope.ajaxChangePos(goods.goods_id, goods.pos, 'edit', goods.cid);
                }


                $scope.ajaxChangePos = function (id, pos, type, cid) {
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_data?action=changePos]]', {
                        id: id,
                        pos: pos,
                        type: type,
                        cid: cid,
                        page: $scope.get["page"]
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.goods = response.goods;
                                return;
                            }
                        });
                }

                $scope.delete = function (id, cid) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_data?action=delete]]', {id: id, page: $scope.get.page, cid: cid})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.goods = response.goods;
                                return;
                            }
                        });
                }

                $scope.search = function () {
                    var get = '?';
                    var i = false;
                    $.each($scope.get, function (name, value) {
                        if (value) {
                            if (i) {
                                get += '&' + name + '=' + value;
                            } else {
                                i = true;
                                get += name + '=' + value;
                            }
                        }
                    });

                    var url = '[[link:admin_goods?action=list]]' + get;
                    window.location.href = url;
                };

                $scope.cancel = function () {
                    var url = '[[link:admin_goods?action=list]]';
                    window.location.href = url;
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.add(file, id,
                            '\\Shop\\Commodity\\Entity\\Goods',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    dfImage.main(data.image_id, 1);
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.image_id == data.image_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };

            }])
    // #NK
    .controller('GoodsEditController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {
                $scope.goods = {};
                $scope.cats = [];
                $scope.form = {};
                $scope.goods_types = [];
                $scope.accs_types = [];
                $scope.categories = [];
                $scope.sections = [];
                $scope.images = [];
                $scope.chara = [];
                $scope.chara_values = [];
                $scope.chara_goods = [];
                $scope.attributes = [];
                $scope.meta = {};
                $scope.ckeditor = [];
                $scope.unit = [];
                $scope.vendors = [];
                $scope.providers = [];
                $scope.abbr = 'none';
                $scope.currency = [];
                $scope.accompanies = [];
                $scope.goods_accompanies = [];
                $scope.types = [];

                $scope.show_select_type = function () {
                    var i = 0;
                    $.each($scope.goods_types, function () {
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }


                $scope.getNameGoods = function (id) {
                    var name = 'Не найден';
                    $.each($scope.accompanies, function (index, goods) {
                        if (goods.goods_id == id) {
                            name = goods.name;
                            return;
                        }
                    });
                    return name;
                }

                $scope.addCat = function (category) {
                    category = category || {cid: 0};
                    var inc = $scope.cats.length;
                    category.inc = inc;
                    $scope.cats.push(category);
                }

                $scope.deleteCat = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.cats[inc].delete = 1;
                }

                $scope.addSection = function (section) {
                    section = section || {status: "1"};
                    var inc = $scope.sections.length;
                    section.inc = inc;
                    $scope.sections.push(section);
                    setTimeout(function () {
                        $scope.ckeditor[inc] = CKEDITOR.replace('text_' + inc);
                    }, 50);
                }

                $scope.deleteSection = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.sections[inc].delete = 1;
                }

                $scope.addChara = function (chara_goods) {
                    chara_goods = chara_goods || {character_id: 0, value_id: "", main: 0};
                    var inc = $scope.chara_goods.length;
                    chara_goods.inc = inc;
                    $scope.chara_goods.push(chara_goods);
                }

                $scope.addValueForm = function (inc) {
                    $scope.form.inc = inc;
                    $scope.form.unit_id = 0;
                    $scope.form.name = '';
                    $scope.form.code = '';
                    $scope.form.info = '';
                    $scope.form.character_id = $scope.chara_goods[inc].character_id;

                    $.magnificPopup.open({
                        items: {
                            src: '#form'
                        },
                        type: 'inline'
                    }, 0);

                }

                $scope.valueFormSave = function () {
                    $http.post('[[link:admin_chara_data?action=addValue]]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.error(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.chara_values[response.value.character_id].push(response.value);
                                $.magnificPopup.close();
                            }
                        });

                };

                $scope.deleteChara = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.chara_goods[inc].delete = 1;
                }

                $scope.selectChara = function (inc) {
                    $scope.chara_goods[inc].value_id = '';
                }

                $scope.addAttr = function (attr) {
                    attr = attr || {};
                    var inc = $scope.attributes.length;
                    attr.inc = inc;
                    attr.delete = 0;
                    $scope.attributes.push(attr);
                }

                $scope.deleteAttr = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.attributes[inc].delete = 1;
                }

                $scope.getCategories = function (typeId) {
                    dfLoading.show('.category_load', 'getCategories');
                    $scope.categories = [];
                    $http.post('[link:admin_category_data?action=categoriesSelect]', {
                        type_id: typeId,
                        placeholder: 'Без категории'
                    })
                        .success(function (response) {
                            dfLoading.hide('.category_load', 'getCategories');
                            $scope.categories = response.categories;
                        });
                }

                $scope.getChara = function () {

                    dfLoading.loading('getChara');
                    $scope.categories = [];
                    $http.post('[link:admin_goods_data?action=getChara]')
                        .success(function (response) {
                            dfLoading.ready('getChara');

                            $scope.chara_values = response.chara_values;
                            $scope.chara = response.chara;
                        });
                }

                $scope.init = function () {
                    $scope.vendors = $window._vendors;
                    $scope.providers = $window._providers;
                    $scope.unit = $window._unit;
                    $scope.chara_values = $window._chara_values;
                    $scope.chara = $window._chara;
                    $scope.meta = $window._meta;
                    $scope.currency = $window._currency;
                    $scope.categories = $window._categories;
                    $scope.accompanies = $window._accompanies;
                    $scope.goods_accompanies = $window._goods_accompanies;
                    $scope.types = $window._types;
                    $scope.goods_types = $window._goods_types;
                    $scope.accs_types = $window._accs_types;

                    $.each($scope.types, function (index, type) {
                        if ($window._list_types[type.id]) {
                            $scope.types[index].status = 1;
                        } else {
                            $scope.types[index].status = 0;
                        }
                    });

                    $scope.images = $window._images;
                    $.each($window._cats, function (index, cat) {
                        $scope.addCat(cat);
                    });
                    $.each($window._sections, function (index, section) {
                        $scope.addSection(section);
                    });
                    $.each($window._chara_goods, function (index, chara_goods) {
                        $scope.addChara(chara_goods);
                    });
                    $.each($window._attributes, function (index, attr) {
                        $scope.addAttr(attr);
                    });

                    $scope.goods = $window._goods;

                    if ($window._cid)
                        $scope.goods.cid = $window._cid;

                    $scope.getCategories($scope.goods.ctype);
                    $scope.getChara();

                    $scope.update();
                }


                $scope.$on('selectGoods', function (ev, goods) {
                    $scope.addAcco(goods);
                });

                $scope.goods_chara_select = null;
                $scope.$on('goodsChara', function (ev, goods) {
                    $scope.goods_chara_select = goods;
                    $.magnificPopup.close();
                    var value = prompt('Скопировать характеристики со значеними, напишите "да" ', 'нет');
                    if (value == 'нет') value = false; else value = true;

                    dfLoading.loading();
                    $http.post('[link:admin_goods_data?action=copyChara]', {
                        goods: $scope.goods_chara_select,
                        value: value
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.errors(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each(response.chara_goods, function (index, chara_goods) {
                                    $scope.addChara(chara_goods);
                                });
                            }
                        });


                });

                $scope.addAcco = function (goods) {
                    var _bool = false;
                    if (goods.goods_id == $scope.goods.goods_id) {
                        dfNotice.error('Нельзя добавить товар самому к себе');
                        return;
                    }

                    $.each($scope.goods_accompanies, function (index, item) {
                        if (item.target_id == goods.goods_id) {
                            _bool = true;
                            return;
                        }
                    });

                    if (_bool) {
                        dfNotice.error('Данные товар уже добавлен');
                        return;
                    }
                    $scope.accompanies.push(goods);
                    dfLoading.loading();

                    $http.post('[[link:admin_goods_data?action=addAcco]]', {
                        current: $scope.goods,
                        target: goods
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.goods_accompanies.push(response.accompany);
                            }
                        });
                };

                $scope.deleteAcco = function (id) {
                    $http.post('[[link:admin_goods_data?action=deleteAcco]]', {id: id});
                    $.each($scope.goods_accompanies, function (index, item) {
                        if (item.id == id) {
                            $scope.goods_accompanies.splice(index, 1);
                            return;
                        }
                    });
                };

                $scope.changeAcco = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_data?action=changeAcco]]', item)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                $scope.goods_accompanies = response.goods_accompanies
                            }
                        });
                };

                $scope.update = function () {
                    setTimeout(function () {
                        $('.chara_goods').each(function () {
                            var value = $(this).data('value');
                            $("option[value='" + value + "']", this).attr("selected", "selected");
                        });

                    }, 100);
                }

                $scope.save = function () {
                    console.log({
                        goods: $scope.goods,
                        meta: $scope.meta,
                        types: $scope.types,
                        attributes: $scope.attributes,
                        sections: sections,
                        chara_goods: $scope.chara_goods,
                        cats: $scope.cats
                    });
                    df.loading('save');

                    var sections = [];
                    $.each($scope.sections, function (inc, section) {
                        section.text = $scope.ckeditor[inc].getData();
                        sections.push(section);
                    });

                    $http.post('[[link:admin_goods_data?action=save]]', {
                        goods: $scope.goods,
                        meta: $scope.meta,
                        types: $scope.types,
                        attributes: $scope.attributes,
                        sections: sections,
                        chara_goods: $scope.chara_goods,
                        cats: $scope.cats
                    })
                        .success(function (response) {
                            df.ready('save');
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.chara_values = response.chara_values;
                                $scope.chara = response.chara;
                                $scope.goods = response.goods;

                                $scope.cats = [];
                                $.each(response.cats, function (index, cat) {
                                    $scope.addCat(cat);
                                });
                                $scope.chara_goods = [];
                                $.each(response.chara_goods, function (index, chara_goods) {
                                    $scope.addChara(chara_goods);
                                });
                                $scope.sections = [];
                                $.each(response.sections, function (index, section) {
                                    $scope.addSection(section);
                                });
                                $scope.attributes = [];
                                $.each(response.attributes, function (index, attr) {
                                    $scope.addAttr(attr);
                                });
                                $scope.update();
                                return;
                            }
                        });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.add(file, id,
                            '\\Shop\\Commodity\\Entity\\Goods',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);

                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    if ($scope.images.length == 0) {
                                        dfImage.main(data.image_id, 1);
                                        data.main = 1;
                                    } else {
                                        data.main = 0;
                                    }
                                    data.pos = 0;
                                    $scope.images.push(data);
                                }
                            });
                    }
                };

                $scope.main_image = function (image, status) {
                    dfImage.main(image.image_id, status);
                    $.each($scope.images, function (index, item) {
                        if (item.image_id == image.image_id) {
                            item.main = status;
                        } else {
                            if (status == 1) {
                                item.main = 0;
                            }
                        }
                    });
                }

                $scope.delete_image = function (item) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }

                    dfLoading.loading();
                    dfImage.delete(item.image_id,
                        function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.images, function (index, image) {
                                    if (image.image_id == item.image_id) {
                                        $scope.images.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                        });
                };

                $scope.saveImage = function (image) {
                    dfLoading.loading();
                    dfImage.save(image,
                        function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };


                $scope.copyGoods = function () {
                    if (!confirm("Вы уверены в том что хоте создать копию товара")) {
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_data?action=copyGoods]]', {id: $scope.goods.goods_id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                window.location.href = response.url;
                            }
                        });
                }

            }])
    .controller('GoodsEditIdsController',
        ['$scope', '$http', 'dfLoading', '$window', '$upload', 'dfNotice',
            function ($scope, $http, dfLoading, $window, $upload, dfNotice) {
                $scope.goods = {};
                $scope.goods_types = [];
                $scope.sections = [];
                $scope.categories = [];
                $scope.images = [];
                $scope.chara = [];
                $scope.ids = [];
                $scope.type_id = 1;
                $scope.chara_goods = [];
                $scope.chara_values = [];
                $scope.attributes = [];
                $scope.meta = {};
                $scope.ckeditor = [];
                $scope.unit = [];
                $scope.vendors = [];
                $scope.providers = [];
                $scope.abbr = 'none';
                $scope.currency = [];

                $scope.show_select_type = function () {
                    var i = 0;
                    $.each($scope.goods_types, function () {
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.getCharaValues = function (character_id) {
                    var arr = [];
                    $.each($scope.chara_values, function (index, item) {
                        if (item.character_id == character_id) {
                            arr.push(item);
                        }
                    });
                    return arr;
                }

                $scope.addSection = function (section) {
                    section = section || {status: "1"};
                    var inc = $scope.sections.length;
                    section.inc = inc;
                    $scope.sections.push(section);
                    setTimeout(function () {
                        $scope.ckeditor[inc] = CKEDITOR.replace('text_' + inc);
                    }, 50);
                }

                $scope.deleteSection = function (inc) {
                    $scope.sections[inc].delete = 1;
                }

                $scope.addChara = function (chara_goods) {
                    chara_goods = chara_goods || {character_id: 0, value_id: "", main: 0};
                    var inc = $scope.chara_goods.length;
                    chara_goods.inc = inc;
                    $scope.chara_goods.push(chara_goods);
                }

                $scope.deleteChara = function (inc) {
                    $scope.chara_goods[inc].delete = 1;
                }
                $scope.selectChara = function (inc) {
                    $scope.chara_goods[inc].value_id = '';
                }

                $scope.addAttr = function (attr) {
                    attr = attr || {};
                    var inc = $scope.attributes.length;
                    attr.inc = inc;
                    attr.show = 1;
                    $scope.attributes.push(attr);
                }

                $scope.deleteAttr = function (inc) {
                    $scope.attributes[inc].delete = 0;
                }

                $scope.getCategories = function (typeId) {
                    dfLoading.show('.category_load', 'getCategories');
                    $scope.categories = [];
                    $http.post('[link:admin_category_data?action=categoriesSelect]', {
                        type_id: typeId,
                        placeholder: 'Без категории'
                    })
                        .success(function (response) {
                            dfLoading.hide('.category_load', 'getCategories');
                            $scope.categories = response.categories;
                        });
                }

                $scope.getChara = function () {

                    dfLoading.loading('getChara');
                    $scope.categories = [];
                    $http.post('[link:admin_goods_data?action=getChara]')
                        .success(function (response) {
                            dfLoading.ready('getChara');

                            $scope.chara_values = response.chara_values;
                            $scope.chara = response.chara;
                        });
                }


                $scope.init = function () {
                    $scope.getCategories($scope.type_id);
                    $scope.getChara();
                    $scope.vendors = $window._vendors;
                    $scope.providers = $window._providers;
                    $scope.unit = $window._unit;
                    $scope.ids = $window._ids;
                    $scope.type_id = $window._type_id;
                    $scope.currency = $window._currency;
                    $scope.categories = $window._categories;
                    $scope.goods_types = $window._goods_types;
                }

                $scope.save = function () {
                    dfLoading.loading();

                    var sections = [];
                    $.each($scope.sections, function (inc, section) {
                        section.text = $scope.ckeditor[inc].getData();
                        sections.push(section);
                    });

                    $http.post('[[link:admin_goods_data?action=saveIds]]', {
                        goods: $scope.goods,
                        meta: $scope.meta,
                        ids: $scope.ids,
                        sections: sections,
                        chara_goods: $scope.chara_goods,
                        attributes: $scope.attributes
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.goods = {};
                                return;
                            }
                        });
                }

            }])
    .controller('CharaListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.chara = [];
                $scope.groups = [];

                $scope.init = function () {
                    $scope.groups = $window._groups;
                    $scope.chara = $window._chara;
                    setTimeout(function () {
                        $('.select_value').each(function () {
                            var value = $(this).data('value');
                            $("option[value='" + value + "']", this).attr("selected", "selected");
                        });
                    }, 100);

                }

                $scope.editGroup = function (chara) {
                    $http.post('[[link:admin_chara_data?action=save]]', {
                        chara: chara,
                        values: []
                    });
                }

                $scope.delete = function (character_id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_chara_data?action=delete]]', {id: character_id});
                    $.each($scope.chara, function (index, chara) {
                        if (chara.character_id == character_id) {
                            $scope.chara.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.up = function (chara) {
                    $scope.ajaxChangePos(chara.character_id, chara.pos, 'up');
                }

                $scope.down = function (chara) {
                    $scope.ajaxChangePos(chara.character_id, chara.pos, 'down');
                }

                $scope.change_pos = function (chara) {
                    $scope.ajaxChangePos(chara.character_id, chara.pos, 'edit');
                }


                $scope.ajaxChangePos = function (id, pos, type) {
                    dfLoading.loading();
                    $http.post('[[link:admin_chara_data?action=changePos]]', {id: id, pos: pos, type: type})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                $scope.chara = response.chara;
                                return;
                            }
                        });
                }

            }])
    .controller('CharaEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.chara = {};
                $scope.values = [];
                $scope.units = [];
                $scope.filters = [];
                $scope.init = function () {
                    $scope.units = $window._units;
                    $scope.filters = $window._filters;
                    $scope.chara = $window._chara;
                    $.each($window._values, function (index, value) {
                        $scope.addValue(value);
                    });
                    setTimeout(function () {
                        $('.select_value').each(function () {
                            var value = $(this).data('value');
                            $("option[value='" + value + "']", this).attr("selected", "selected");
                        });
                    }, 100);
                }


                $scope.addValue = function (value) {
                    value = value || {unit_id: 0};
                    var inc = $scope.values.length;
                    value.inc = inc;
                    value.show = 1;
                    $scope.values.push(value);
                }

                $scope.deleteValue = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.values[inc].show = 0;
                    if ($scope.values[inc].value_id) {
                        $http.post('[[link:admin_chara_data?action=deleteValue]]', {
                            value_id: $scope.values[inc].value_id
                        });

                    }
                }

                $scope.save = function () {
                    var values = [];
                    $.each($scope.values, function (inc, value) {
                        if ($scope.values[inc].show == 1) {
                            values.push(value);
                        }
                    });

                    dfLoading.loading();
                    $http.post('[[link:admin_chara_data?action=save]]', {
                        chara: $scope.chara,
                        values: values
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                window.location.reload(true);
                            }
                        });

                }


            }]).controller('CurrencyListCtrl',
    ['$scope', '$http', '$window', 'dfLoading', 'dfNotice',
        function ($scope, $http, $window, dfLoading, dfNotice) {
            $scope.currency = [];
            $scope.config = [];
            $scope.init = function () {
                $scope.currency = $window._currency;
                $scope.config = $window._config;
            }

            $scope.edit = function (item) {
                dfLoading.loading();
                $http.post('[[link:admin_currency_data?action=save]]', item)
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }
                    });
            }

            $scope.refresh = function () {
                $http.post('[[link:admin_currency_data?action=refresh]]')
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }

                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            $scope.currency = response.currency;
                        }
                    });
            }


            $scope.isDefault = function (code) {
                if ($scope.config.currency.code == code)
                    return true;
                else
                    return false;
            }

            $scope.delete = function (currency_id) {
                if (!confirm('Вы действительно хотите удалить?')) {
                    return false;
                }
                $http.post('[[link:admin_currency_data?action=delete]]', {id: currency_id});
                $.each($scope.currency, function (index, currency) {
                    if (currency.currency_id == currency_id) {
                        $scope.currency.splice(index, 1);
                        return false;
                    }
                });
            }

        }])
    .controller('CurrencyEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.currency = {};
                $scope.types = [];

                $scope.init = function () {
                    $scope.types = $window._types;

                    setTimeout(function () {
                        $scope.$apply(function () {
                            $scope.currency = $window._currency;
                        });
                    }, 20);
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_currency_data?action=save]]', $scope.currency)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.currency = response.currency;
                            }
                        });

                }


            }])
    .controller('OrderListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', '$sce',
            function ($scope, $http, dfLoading, $window, dfNotice, $sce) {
                $scope.status = {};
                $scope.users = [];
                $scope.status = [];
                $scope.options = [];
                $scope.get = [];

                $scope.init = function () {
                    $scope.status = $window._status;
                    $scope.options = $window._options;
                    $scope.users = $window._users;
                    $scope.orders = $window._orders;
                    $scope.get = $window._get;

                };

                $scope.html = function (html_code) {
                    return $sce.trustAsHtml(html_code);
                }

                $scope.truncate = truncate;

                $scope.re_status = function (status, id) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=update]]', {status: status, id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.ok) {

                                $.each($scope.orders, function (index, order) {
                                    if (order.order_id == id) {
                                        $scope.orders[index] = response.order;
                                    }
                                });

                                dfNotice.ok(response.ok)
                            }
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                        });
                };


                $scope.isNotEditNote = function (item) {
                    if ($scope.isEdit == null)
                        return true;

                    if ($scope.isEdit.order_id == item.order_id)
                        return false;
                    else
                        return true;
                }

                $scope.startEditNote = function (item) {
                    $scope.isEdit = item;
                }

                $scope.cancelEditNote = function () {
                    $scope.isEdit = null;
                }

                $scope.saveEditNote = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=update]]', {note: item.note, id: item.order_id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                $scope.isEdit = null;
                                dfNotice.ok(response.ok);
                            }

                        }).error(function (response) {
                        dfLoading.ready();
                        dfNotice.error('Произошла ошибка');
                    });


                };
            }])
    .controller('OrderEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {

                $scope.user = [];
                $scope.items = [];
                $scope.status = [];
                $scope.options = [];
                $scope.order = [];

                $scope.init = function () {
                    $scope.user = window._user;
                    $scope.items = window._items;
                    $scope.status = window._status;
                    $scope.options = window._options;
                    $scope.order = $window._order;
                };


                $scope.price = function (value) {
                    value = df.shop.number_format(value, 2, '.', '');
                    return value;
                }

                $scope.showTextArea = function (option) {
                    if (
                        option.code == 'address' ||
                        option.code == 'comment' ||
                        option.code == 'details'
                    ) {
                        return true;
                    }
                    return false;
                }


                $scope.showOption = function (option) {
                    if (option.code == 'email') {
                        return false;
                    }
                    return true;
                }

                $scope.editOption = function (opt) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=option]]', opt)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                        });
                }

                $scope.editOrder = function (order) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=update]]', order)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                            }
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                        });
                }

                $scope.editItem = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=item]]', item)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.ok) {
                                $scope.order = response.order;
                            }
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                        });
                }

                $scope.deleteItem = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=itemDelete]]', item)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.order) {
                                $scope.order = response.order;

                                $.each($scope.items, function (index, i) {
                                    if (i.id == item.id) {
                                        $scope.items.splice(index, 1);
                                        return false;
                                    }
                                });

                            }
                            if (response.error) {
                                dfNotice.error(response.error);
                            }
                        });
                }

                $scope.editDiscount = function (discount) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=discount]]', {
                        discount: discount,
                        order_id: $scope.order.order_id
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.order) {
                                $scope.order = response.order;
                            }
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                        });
                }

                $scope.editDelivery = function (delivery) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=delivery]]', {
                        delivery: delivery,
                        order_id: $scope.order.order_id
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.order) {
                                $scope.order = response.order;
                            }
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                        });
                }

                $scope.re_status = function (status, id) {
                    dfLoading.loading();
                    $http.post('[[link:admin_order_data?action=update]]', {status: status, id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                var status_name;
                                $.each($scope.status, function (index, st) {
                                    if (st.id == status) {
                                        status_name = st.name;
                                        return true;
                                    }
                                });
                                $scope.owner.status_name = status_name;
                            }
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                        });
                };

            }])
    .controller('TypeGoodsListCtrl',
        ['$scope', '$window',
            function ($scope, $window) {
                $scope.types = [];

                $scope.init = function () {
                    $scope.types = $window._types;
                }
            }])
    .controller('UnitGoodsListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http',
            function ($scope, $window, dfNotice, dfLoading, $http) {
                $scope.units = [];
                $scope.form = {};

                $scope.init = function () {
                    $scope.units = $window._units;
                }

                $scope.edit = function (unit) {
                    $http.post('[[link:admin_unit_data?action=save]]', unit)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok)
                            }
                            $scope.units = response.units;
                        });
                }

                $scope.delete = function (unit) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_unit_data?action=delete]]', unit);
                    $.each($scope.units, function (index, item) {
                        if (item.unit_id == unit.unit_id) {
                            $scope.units.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_unit_data?action=save]]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {};
                            }
                            $scope.units = response.units;
                        });
                }


            }])
    .controller('VendorGoodsListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', 'dfImage',
            function ($scope, $window, dfNotice, dfLoading, $http, dfImage) {
                $scope.vendors = [];
                $scope.images = [];
                $scope.form = {};

                $scope.init = function () {
                    $scope.vendors = $window._vendors;
                    $scope.images = $window._images;
                }

                $scope.getImageSrc = function (goods_id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == goods_id) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                }

                $scope.edit = function (vendor) {
                    $http.post('[[link:admin_vendor_data?action=save]]', {
                        vendor: vendor,
                        meta: [],
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $.each($scope.vendors, function (index, item) {
                                    if (item.vendor_id == response.vendor.vendor_id) {
                                        $scope.vendors[index] = response.vendor
                                        return false;
                                    }
                                });
                            }
                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_vendor_data?action=delete]]', {id: id});
                    $.each($scope.vendors, function (index, item) {
                        if (item.vendor_id == id) {
                            $scope.vendors.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_vendor_data?action=save]]', {
                        vendor: $scope.form,
                        meta: [],
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {};

                                temp = angular.copy($scope.vendors);
                                $scope.vendors = new Array();
                                $scope.vendors.push(response.vendor);
                                $.each(temp, function (index, item) {
                                    $scope.vendors.push(item);
                                });
                            }

                        });
                };

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.set(file, id,
                            '\\Shop\\Commodity\\Entity\\Vendor',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.image_id == data.image_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                            return;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };

            }])
    .controller('ProviderGoodsListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', 'dfImage',
            function ($scope, $window, dfNotice, dfLoading, $http, dfImage) {
                $scope.providers = [];
                $scope.form = {};

                $scope.init = function () {
                    $scope.providers = $window._providers;
                }

                $scope.edit = function (vendor) {
                    $http.post('[[link:admin_provider_data?action=save]]', {
                        provider: provider
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $.each($scope.providers, function (index, item) {
                                    if (item.id == response.provider.id) {
                                        $scope.providers[index] = response.provider;
                                        return false;
                                    }
                                });
                            }
                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_provider_data?action=delete]]', {id: id});
                    $.each($scope.providers, function (index, item) {
                        if (item.id == id) {
                            $scope.providers.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_provider_data?action=save]]', {
                        provider: $scope.form
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {};

                                temp = angular.copy($scope.providers);
                                $scope.providers = [];
                                $scope.providers.push(response.provider);
                                $.each(temp, function (index, item) {
                                    $scope.providers.push(item);
                                });
                            }

                        });
                };

            }])
    .controller('GroupCharaGoodsListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http',
            function ($scope, $window, dfNotice, dfLoading, $http) {
                $scope.groups = [];
                $scope.form = {};

                $scope.init = function () {
                    $scope.groups = $window._groups;
                }

                $scope.edit = function (group) {
                    $http.post('[[link:admin_chara_data?action=saveGroup]]', group)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok)
                            }
                            $scope.groups = response.groups;
                        });
                }

                $scope.delete = function (group) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_chara_data?action=deleteGroup]]', group);
                    $.each($scope.groups, function (index, item) {
                        if (item.group_id == group.group_id) {
                            $scope.groups.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_chara_data?action=saveGroup]]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {};
                            }
                            $scope.groups = response.groups;
                        });
                }


            }])
    .controller('GoodsTypeListCtrl',
        ['$scope', '$window', '$http', 'dfNotice', 'dfLoading',
            function ($scope, $window, $http, dfNotice, dfLoading) {
                $scope.goods = [];
                $scope.goods_list = [];
                $scope.images = {};
                $scope.images_list = [];
                $scope.type_id = null;
                $scope.types = [];
                $scope.name = '';

                $scope.init = function () {
                    $scope.goods = $window._goods;
                    $scope.images = $window._images;
                    $scope.type_id = $window._type_id;
                    $scope.types = $window._types;
                }

                $scope.search = function () {
                    if ($scope.name.length < 4) {
                        dfNotice.error('Укажите название товара не меньше 4 знаков');
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_type_data?action=goods]]', {name: $scope.name})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.goods) {
                                $scope.goods_list = response.goods;
                                $scope.images_list = response.images;
                                $scope.types = response.types;
                            }
                        });
                }

                $scope.getImageSrc = function (goods_id, list) {
                    var src = '/source/images/no.png';
                    if (!list) {
                        $.each($scope.images, function (index, image) {
                            if (image.target_id == goods_id) {
                                src = image.preview;
                            }
                        });
                    } else {
                        $.each($scope.images_list, function (index, image) {
                            if (image.target_id == goods_id) {
                                src = image.preview;
                            }
                        });
                    }
                    return src;
                }


                $scope.add = function (inner_goods) {
                    var isset = false;
                    $.each($scope.goods, function (index, item) {
                        if (item.goods_id == inner_goods.goods_id) {
                            isset = true;
                            return;
                        }
                    })

                    if (isset) {
                        dfNotice.error('Товар уже добавлен');
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_type_data?action=add]]?type_id=' + $scope.type_id, inner_goods)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.ok) {
                                $scope.transferImage(inner_goods);
                                $scope.goods.push(inner_goods);
                                $scope.types = response.types;
                                dfNotice.ok(response.ok);
                            }
                            if (response.error) {
                                dfNotice.error(response.error);
                            }
                        });
                }

                $scope.transferImage = function (item) {
                    var inner_image = null;
                    $.each($scope.images_list, function (index, image) {
                        if (image.target_id == item.goods_id) {
                            inner_image = image;
                        }
                    });

                    if (inner_image) {
                        console.log($scope.images);
                        $scope.images[item.goods_id] = inner_image;
                    }
                }

                $scope.delete = function (goods_id, type_id) {
                    if (!confirm('Вы действительно хотите удалить из этого списка?')) {
                        return false;
                    }
                    $http.post('[[link:admin_goods_type_data?action=delete]]', {type_id: type_id, goods_id: goods_id});

                    $.each($scope.goods, function (index, item) {
                        if (item.goods_id == goods_id) {
                            $scope.goods.splice(index, 1);
                            return false;
                        }
                    });
                }


                $scope.up = function (goods, type_id) {
                    $scope.ajaxChangePos(goods.goods_id, type_id, 'up');
                }

                $scope.down = function (goods, type_id) {
                    $scope.ajaxChangePos(goods.goods_id, type_id, 'down');
                }

                $scope.ajaxChangePos = function (goods_id, type_id, type) {
                    dfLoading.loading();
                    $http.post('[[link:admin_goods_type_data?action=changePos]]', {
                        goods_id: goods_id,
                        type_id: type_id,
                        type: type
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                $scope.goods = response.goods;
                                $scope.types = response.types;
                                return;
                            }
                        });
                }

                $scope.getPos = function (goods) {
                    var pos = 0;
                    $.each($scope.types, function (index, type) {
                        if (type.goods_id == goods.goods_id) {
                            pos = type.pos;
                            return;
                        }
                    });
                    return pos;
                }
            }])
    .controller('GoodsReviewsListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http',
            function ($scope, $window, dfNotice, dfLoading, $http) {
                $scope.reviews = [];
                $scope.form = {};

                $scope.init = function () {
                    $scope.reviews = $window._reviews;
                    $scope.users = $window._users;
                    $scope.goods = $window._goods;
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[[link:admin_reviews_data?action=status]]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.reviews, function (index, reviews) {
                                    if (reviews.review_id == id) {
                                        $scope.reviews[index].status = status;
                                        return;
                                    }
                                });
                            }

                        });
                }

                $scope.edit = function (vendor) {
                    $http.post('[[link:admin_reviews_data?action=save]]', vendor)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok)
                            }
                            $scope.reviews = response.reviews;
                        });
                }

                $scope.delete = function (vendor) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_reviews_data?action=delete]]', vendor);
                    $.each($scope.reviews, function (index, item) {
                        if (item.vendor_id == vendor.vendor_id) {
                            $scope.reviews.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_reviews_data?action=save]]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {};
                            }
                            $scope.reviews = response.reviews;
                        });
                }

            }])
    .controller('GoodsReviewController',
        ['$scope', '$http', 'dfLoading', '$window', '$upload', 'dfNotice',
            function ($scope, $http, dfLoading, $window, $upload, dfNotice) {
                $scope.review = {};

                $scope.init = function () {
                    $scope.review = $window._review;
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_reviews_data?action=save]]', {review: $scope.review})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                var href = '[[link:admin_reviews?action=edit]]?id=' + response.review.review_id;
                                window.location.href = href;
                            }
                        });
                }
            }])
    .controller('VendorEditController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {
                $scope.vendor = {};
                $scope.image = {};
                $scope.meta = {};
                $scope.ckeditor = null;

                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('text');

                    $scope.vendor = $window._vendor;
                    $scope.meta = $window._meta;
                    $scope.image = $window._image;
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $scope.vendor.text = $scope.ckeditor.getData();

                    $http.post('[[link:admin_vendor_data?action=save]]', {
                        vendor: $scope.vendor,
                        meta: $scope.meta
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.vendor = response.vendor;
                                return;
                            }
                        });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\Shop\\Commodity\\Entity\\Vendor',
                            function (data, status, headers, config) {
                                dfLoading.ready();
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    $scope.image = data;
                                }
                            });
                    }
                };

            }])
    .controller('ProviderEditController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {
                $scope.provider = {};
                $scope.ckeditor = null;

                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('text');
                    $scope.provider = $window._provider;
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $scope.provider.text = $scope.ckeditor.getData();

                    $http.post('[[link:admin_provider_data?action=save]]', {
                        provider: $scope.provider
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.provider = response.provider;
                                return;
                            }
                        });
                }
            }])
    .controller('CollectionListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', 'dfImage',
            function ($scope, $window, dfNotice, dfLoading, $http, dfImage) {
                $scope.collections = [];
                $scope.categories = [];
                $scope.collection_categoriescategories = [];
                $scope.images = [];
                $scope.types = [];
                $scope.get = {};

                $scope.init = function () {
                    $scope.collections = $window._collections;
                    $scope.categories = $window._categories;
                    $scope.collection_categories = $window._collection_categories;
                    $scope.types = $window._types;
                    $scope.images = $window._images;
                    $scope.get = $window._get;
                }

                $scope.save = function (item) {
                    dfLoading.loading();

                    $http.post('[[link:admin_collection_data?action=save]]', {
                        collection: item,
                        meta: [],
                        attributes: [],
                        packages: [],
                        packages_goods: [],
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }

                $scope.show_select_type = function () {
                    var i = 0;
                    $.each($scope.types, function () {
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                };

                $scope.getNameCategory = function (id) {
                    var name = 'Без категории';
                    if (id == 0) {
                        return name;
                    }
                    $.each($scope.collection_categories, function (index, cat) {
                        if (cat.cid == id) {
                            name = cat.name;
                            return;
                        }
                    });
                    return name;
                }

                $scope.select_type = function (typeId) {

                    $scope.get.type_id = typeId;
                    $scope.get.cid = 0;
                    dfLoading.loading();
                    $scope.categories = [];
                    $http.post('[[link:admin_category_data?action=categoriesSelect]]', {
                        type_id: typeId,
                        placeholder: 'Все категории'
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.categories = response.categories;
                        });

                }

                $scope.getImageSrc = function (id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                }

                $scope.search = function () {
                    var get = '?';
                    var i = false;
                    $.each($scope.get, function (name, value) {
                        if (value) {
                            if (i) {
                                get += '&' + name + '=' + value;
                            } else {
                                i = true;
                                get += name + '=' + value;
                            }
                        }
                    });

                    var url = '[[link:admin_collection?action=list]]' + get;
                    window.location.href = url;
                };

                $scope.cancel = function () {
                    var url = '[[link:admin_collection?action=list]]';
                    window.location.href = url;
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.add(file, id,
                            '\\Shop\\Commodity\\Entity\\Collection',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    dfImage.main(data.image_id, 1);
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.image_id == data.image_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };

            }])
    .controller('CollectionEditCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', 'dfImage',
            function ($scope, $window, dfNotice, dfLoading, $http, dfImage) {
                $scope.collection = {};
                $scope.categories = [];
                $scope.attributes = [];
                $scope.packages = [];
                $scope.packages_goods = [];
                $scope.goods = [];
                $scope.meta = [];
                $scope.vendors = [];
                $scope.images = [];
                $scope.types = [];
                $scope.ckeditor = null;
                $scope.reload = 0;
                $scope.form_pack = {};

                $scope.init = function (reload) {
                    $scope.reload = reload;
                    $scope.ckeditor = CKEDITOR.replace('text');

                    $scope.collection = $window._collection;
                    $scope.categories = $window._categories;
                    $scope.vendors = $window._vendors;
                    $scope.types = $window._types;
                    $scope.meta = $window._meta;
                    $scope.images = $window._images;
                    $scope.goods = $window._goods;

                    $.each($window._attributes, function (index, attr) {
                        $scope.addAttr(attr);
                    });

                    $.each($window._packages, function (index, pack) {
                        $scope.addPackage(pack);
                    });

                    $.each($window._packages_goods, function (index, pack) {
                        $scope.addPackageGoods(pack);
                    });

                };

                $scope.save = function () {
                    $scope.collection.text = $scope.ckeditor.getData();

                    dfLoading.loading();

                    $http.post('[[link:admin_collection_data?action=save]]', {
                        collection: $scope.collection,
                        meta: $scope.meta,
                        attributes: $scope.attributes,
                        packages: $scope.packages,
                        packages_goods: $scope.packages_goods
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                if ($scope.reload) {
                                    window.location.href = '[[link:admin_collection?action=edit]]?id=' + response.collection.id;
                                }
                                dfNotice.ok(response.ok);
                                $scope.collection = response.collection;
                                $scope.goods = response.goods;

                                $scope.attributes = [];
                                $.each(response.attributes, function (index, attr) {
                                    $scope.addAttr(attr);
                                });

                                $scope.packages = [];
                                $.each(response.packages, function (index, pack) {
                                    $scope.addPackage(pack);
                                });

                                $scope.packages_goods = [];
                                $.each(response.packages_goods, function (index, pack) {
                                    $scope.addPackageGoods(pack);
                                });

                                return;
                            }
                        });
                }

                $scope.show_select_type = function () {
                    var i = 0;
                    $.each($scope.types, function () {
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.select_type = function (typeId) {

                    $scope.collection.ctype = typeId;
                    $scope.collection.cid = 0;
                    dfLoading.loading();
                    $scope.categories = [];
                    $http.post('[[link:admin_category_data?action=categoriesSelect]]', {
                        type_id: typeId,
                        placeholder: 'Выберите категорию'
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.categories = response.categories;
                        });

                }


                $scope.addAttr = function (attr) {
                    attr = attr || {};
                    var inc = $scope.attributes.length;
                    attr.inc = inc;
                    attr.delete = 0;
                    $scope.attributes.push(attr);
                }

                $scope.deleteAttr = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.attributes[inc].delete = 1;
                }


                $scope.$on('selectGoods', function (ev, goods) {
                    $scope.addGoods(goods);
                });

                $scope.getNameGoods = function (id) {
                    var name = 'Не найден';
                    $.each($scope.goods, function (index, goods) {
                        if (goods.goods_id == id) {
                            name = goods.name;
                            return;
                        }
                    });
                    return name;
                }

                $scope.addPackageGoods = function (package_goods) {
                    var inc = $scope.packages_goods.length;
                    var item = {
                        inc: inc,
                        id: package_goods.id,
                        goods_id: package_goods.goods_id,
                        package_id: package_goods.package_id,
                        pos: package_goods.pos
                    };
                    $scope.packages_goods.push(item);
                }

                $scope.addGoods = function (goods) {
                    var _bool = false;
                    var _add = false;
                    $.each($scope.packages_goods, function (index, item) {
                        if (item.goods_id == goods.goods_id) {
                            if (item.delete == 1) {
                                $scope.packages_goods[index].delete = 0;
                                _add = true;
                            } else {
                                _bool = true;
                            }
                            return;
                        }
                    });
                    if (_bool) {
                        dfNotice.error('Данные товар уже добавлен');
                        return;
                    }
                    if (_add) {
                        return;
                    }
                    var inc = $scope.packages_goods.length;
                    var item = {goods_id: goods.goods_id, package_id: 0, inc: inc, pos: 0};
                    $scope.packages_goods.push(item);
                    $scope.goods.push(goods);
                    dfNotice.ok('Товар добавлен к коллекции');
                }

                $scope.deleteGoods = function (inc) {
                    $scope.packages_goods[inc].delete = 1;
                }

                $scope.addPackage = function (pack) {
                    $scope.form_pack = {};
                    pack = pack || {};
                    var inc = $scope.packages.length;
                    pack.inc = inc;
                    pack.delete = 0;
                    $scope.packages.push(pack);
                }

                $scope.deletePackage = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.packages[inc].delete = 1;
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.add(file, id,
                            '\\Shop\\Commodity\\Entity\\Collection',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    if ($scope.images.length == 0) {
                                        dfImage.main(data.image_id, 1);
                                        data.main = 1;
                                    } else {
                                        data.main = 0;
                                    }
                                    data.pos = 0;
                                    $scope.images.push(data);
                                }
                            });
                    }
                };

                $scope.main_image = function (image, status) {
                    dfImage.main(image.image_id, status);
                    $.each($scope.images, function (index, item) {
                        if (item.image_id == image.image_id) {
                            item.main = status;
                        } else {
                            if (status == 1) {
                                item.main = 0;
                            }
                        }
                    });
                }

                $scope.delete_image = function (item) {
                    dfLoading.loading();
                    dfImage.delete(item.image_id,
                        function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.images, function (index, image) {
                                    if (image.image_id == item.image_id) {
                                        $scope.images.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                        });
                };

                $scope.saveImage = function (image) {
                    dfLoading.loading();
                    dfImage.save(image,
                        function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };

            }])
    .controller('GoodsPopupCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', '$rootScope',
            function ($scope, $window, dfNotice, dfLoading, $http, $rootScope) {
                $scope.goods = [];
                $scope.form = {name: null, cid: 0, article: null};
                $scope.typeId = 1;
                $scope.option = null;
                $scope.form_adv = 0;
                $scope.pagination = {};
                $scope.btn = 0;
                $scope.select = 'selectGoods';

                $scope.init = function (typeId, cid, select, option) {
                    $scope.form.cid = cid;
                    $scope.option = option;
                    $scope.selectType(typeId);
                    $scope.search();
                    if (select) {
                        $scope.select = select;
                    }
                }

                $scope.next = function () {
                    $scope.load($scope.pagination.next);
                }

                $scope.search = function () {
                    $scope.load();
                };


                $scope.load = function (page) {
                    dfLoading.loading('search');
                    $http.post('[[link:admin_goods_data?action=goods]]', {
                        form: $scope.form,
                        type_id: $scope.typeId,
                        page: page
                    })
                        .success(function (response) {
                            dfLoading.ready('search');
                            $scope.pagination = response.pagination;
                            if (!page) {
                                $scope.goods = response.goods;
                                if ($scope.pagination.is_last) {
                                    $scope.btn = 0;
                                } else {
                                    $scope.btn = 1;
                                }
                            }
                            else if (response.goods.length) {
                                $.each(response.goods, function (index, product) {
                                    $scope.goods.push(product);
                                });

                                if ($scope.pagination.is_last) {
                                    $scope.btn = 0;
                                } else {
                                    $scope.btn = 1;
                                }
                            }

                        });
                }

                $scope.addGoods = function (goods) {
                    goods.option = $scope.option;
                    $rootScope.$broadcast($scope.select, goods);
                }

                $scope.getGoods = function (typeId) {
                    return $scope.goods;
                }

                $scope.selectType = function (typeId) {
                    $scope.typeId = typeId;
                    $scope.getCategories(typeId);
                }

                $scope.getCategories = function (typeId) {
                    dfLoading.show('.category_load', 'getCategories');
                    $scope.categories = [];
                    $http.post('[link:admin_category_data?action=categoriesSelect]', {
                        type_id: typeId,
                        placeholder: 'Все категории'
                    })
                        .success(function (response) {
                            dfLoading.hide('.category_load', 'getCategories');
                            $scope.categories = response.categories;
                        });
                }

                $scope.$on('selectTypeGoods', function (ev, typeId) {
                    $scope.selectType(typeId);
                });

            }])
    .controller('GoodsPopupAdsCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', '$rootScope',
            function ($scope, $window, dfNotice, dfLoading, $http, $rootScope) {
                $scope.goods = [];
                $scope.form = {name: null, cid: 0, article: null};
                $scope.typeId = 1;
                $scope.option = null;
                $scope.form_adv = 0;
                $scope.pagination = {};
                $scope.btn = 0;
                $scope.select = 'selectGoods';

                $scope.init = function (typeId, cid, gid, select, option) {
                    $scope.form.cid = cid;
                    $scope.form.gid = gid;
                    $scope.option = option;
                    $scope.selectType(typeId);
                    $scope.search();
                    if (select) {
                        $scope.select = select;
                    }
                }

                $scope.next = function () {
                    $scope.load($scope.pagination.next);
                }

                $scope.search = function () {
                    $scope.load();
                };


                $scope.load = function (page) {
                    dfLoading.loading('search');
                    $http.post('[[link:admin_goods_data?action=goods]]', {
                        form: $scope.form,
                        type_id: $scope.typeId,
                        page: page
                    })
                        .success(function (response) {
                            dfLoading.ready('search');
                            $scope.pagination = response.pagination;
                            if (!page) {
                                $scope.goods = response.goods;
                                if ($scope.pagination.is_last) {
                                    $scope.btn = 0;
                                } else {
                                    $scope.btn = 1;
                                }
                            }
                            else if (response.goods.length) {
                                $.each(response.goods, function (index, product) {
                                    $scope.goods.push(product);
                                });

                                if ($scope.pagination.is_last) {
                                    $scope.btn = 0;
                                } else {
                                    $scope.btn = 1;
                                }
                            }

                        });
                }

                $scope.copyGoods = function (goods) {
//123

                    if (goods.goods_id == $scope.form.gid) {
                        dfNotice.error('Это тот же товар');
                        return;
                    }

                    $scope.form.type = 'copy_goods';
                    $http.post('[link:admin_goods_data?action=copyGoodsAds]', {
                        form: $scope.form,
                        goods: goods
                    })
                        .success(function (response) {
                            dfNotice.ok('Товары добавлены');
                        });

                }

                $scope.copyGoodsCats = function () {

                    $scope.form.type = 'copy_cats';
                    $http.post('[link:admin_goods_data?action=copyGoodsCatsAds]', {
                        form: $scope.form
                    })
                        .success(function (response) {
                            console.log(response);
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            dfNotice.ok('Товары добавлены');
                        });
                }

                $scope.getGoods = function (typeId) {
                    return $scope.goods;
                }

                $scope.selectType = function (typeId) {
                    $scope.typeId = typeId;
                    $scope.getCategories(typeId);
                }

                $scope.getCategories = function (typeId) {
                    dfLoading.show('.category_load', 'getCategories');
                    $scope.categories = [];
                    $http.post('[link:admin_category_data?action=categoriesSelect]', {
                        type_id: typeId,
                        placeholder: 'Все категории'
                    })
                        .success(function (response) {
                            dfLoading.hide('.category_load', 'getCategories');
                            $scope.categories = response.categories;
                        });
                }

            }])
    .controller('GoodsOptionsListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {

                $scope.options = [];

                $scope.init = function () {
                    $scope.options = $window._options;
                }

                $scope.change = function (option) {
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=save]]', {
                        option: option
                    }).success(function (response) {
                        dfLoading.ready();
                    });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            $.each($scope.options, function (index, item) {
                                if (item.id == id) {
                                    $scope.options.splice(index, 1);
                                    return false;
                                }
                            });
                        });
                }

            }])
    .controller('GoodsOptionEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {

                $scope.option = {};
                $scope.form = {type: "1"};
                $scope.types = [];
                $scope.variants = [];
                $scope.variant_types = [];
                $scope.goods = {};
                $scope.images = [];
                $scope.ckeditor = null;

                $scope.type_text = []

                $scope.show_variants = function () {
                    if ($scope.option.id == null) {
                        return false;
                    }
                    if ($scope.option.type == 4 || $scope.option.type == 5) {
                        return false;
                    }
                    return true;
                }

                $scope.is_flag = function () {
                    if ($scope.option.type == 3) {
                        return true;
                    }
                    return false;
                }

                $scope.is_text = function () {
                    if ($scope.option.type == 4 || $scope.option.type == 5) {
                        return true;
                    }
                    return false;
                }

                $scope.init = function () {
                    $scope.option = $window._option;
                    $scope.types = $window._types;
                    $scope.goods = $window._goods;
                    $scope.variants = $window._variants;
                    $scope.variant_types = $window._variant_types;
                    $scope.images = $window._images;

                    $scope.ckeditor = CKEDITOR.replace('text');
                }


                $scope.addVariant = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=saveVariant]]', {
                        variant: $scope.form,
                        option: $scope.option
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.variants.push(response.variant);
                                $scope.form = {};
                            }
                        });
                };

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=deleteVariant]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            $.each($scope.variants, function (index, item) {
                                if (item.id == id) {
                                    $scope.variants.splice(index, 1);
                                    return false;
                                }
                            });
                        });
                }

                $scope.edit = function (variant) {
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=saveVariant]]', {
                        variant: variant,
                        option: $scope.option
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);

                                $.each($scope.variants, function (index, item) {
                                    if (item.id == response.variant.id) {
                                        $scope.variants[index] = response.variant;
                                    }
                                });
                            }
                        });
                };

                $scope.save = function () {
                    $scope.option.text = $scope.ckeditor.getData();
                    $scope.option.goods_id = $scope.goods.goods_id;
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=save]]', {
                        option: $scope.option
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);

                                if (!$scope.option.id) {
                                    $window.location.href = '[[link:admin_option?action=edit]]?id=' + response.option.id;
                                } else {
                                    $scope.option = response.option;
                                }
                            }
                        });
                };


                $scope.getImageId = function (id) {
                    var _id = 0;
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            _id = image.image_id;
                            return;
                        }
                    });
                    return _id;
                }

                $scope.getImageSrc = function (id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.set(file, id,
                            '\\Shop\\Commodity\\Entity\\Options\\Variant',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.image_id == data.image_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };

            }])
    .controller('GoodsOptionVariantEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {

                $scope.variant = {};
                $scope.option = {};
                $scope.image = null;
                $scope.ckeditor = null;

                $scope.init = function () {

                    $scope.variant = $window._variant;
                    $scope.option = $window._option;
                    $scope.image = $window._image;

                    $scope.ckeditor = CKEDITOR.replace('text');
                };

                $scope.save = function () {
                    $scope.variant.text = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=saveVariant]]', {
                        variant: $scope.variant,
                        option: $scope.option,
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                            }
                        });
                };

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\Shop\\Commodity\\Entity\\Options\\Variant',
                            function (data, status, headers, config) {
                                dfLoading.ready();
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    $scope.image = data;
                                }
                            });
                    }
                };

            }])
    .controller('GoodsOptionCombinationEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {

                $scope.options = [];
                $scope.variants = [];
                $scope.combinations = [];
                $scope.images = [];
                $scope.goods = {};

                $scope.init = function () {
                    $scope.options = $window._options;
                    $scope.variants = $window._variants;
                    $scope.combinations = $window._combinations;
                    $scope.images = $window._images;
                    $scope.goods = $window._goods;
                }

                $scope.change = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=saveCombination]]', {
                        combination: item
                    }).success(function (response) {
                        dfLoading.ready();
                    });
                }

                $scope.generation = function (update) {
                    if (!confirm('Вы действительно хотите сгенерировать комбинации?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=genCombinations]]', {
                        id: $scope.goods.goods_id,
                        update: update
                    }).success(function (response) {
                        dfLoading.ready();
                        window.location.reload();
                    });
                }

                $scope.getImageSrc = function (id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.set(file, id,
                            '\\Shop\\Commodity\\Entity\\Options\\Inventory',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.image_id == data.image_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_option_data?action=deleteCombination]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }

                            $.each($scope.combinations, function (index, item) {
                                if (item.combination_hash == id) {
                                    $scope.combinations.splice(index, 1);
                                    return false;
                                }
                            });

                        });
                }

            }])
    .controller('YmlEditCtrl', ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
        function ($scope, $http, dfLoading, $window, dfNotice) {
            $scope.categories = [];
            $scope.show_categories = [];
            $scope.select_types = null;
            $scope.yml = {config: []};

            $scope.init = function (typeId) {
                $scope.yml = $window._yml;
                $scope.select(typeId);
            };

            $scope.select = function (typeId) {
                $scope.select_types = typeId;
                dfLoading.loading();
                $scope.categories = [];
                $http.post('[[link:admin_category_data?action=categories]]', {type_id: typeId})
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.categories = response.categories;
                        $.each($scope.categories, function (index, cat) {
                            if ($scope.yml.config[cat.cid] == cat.cid) {
                                $scope.categories[index].check = true;
                            } else {
                                $scope.categories[index].check = false;
                            }
                        })
                    });
            };


            $scope.getCategories = function (pid) {
                var categories = [];
                $.each($scope.categories, function (index, cat) {
                    if (cat.pid == pid) {
                        categories.push(cat);
                    }
                });
                return categories;
            }

            $scope.show_category = function (category) {
                if ($scope.show_categories[category.cid]) {
                    return true;
                }
                return false;
            }

            $scope.show_category_child = function (category) {
                if ($scope.show_categories[category.cid]) {
                    $scope.show_categories[category.cid] = false;
                }
                else {
                    $scope.show_categories[category.cid] = true;
                }
            }

            $scope.select_category = function (category) {
                if (!$('#cat_chb_' + category.cid).prop("checked")) {
                    $('#cat_' + category.cid + ' input').each(function (index, checkbox) {
                        $(checkbox).removeAttr("checked", "checked");
                    });
                } else {
                    $('#cat_' + category.cid + ' input').each(function (index, checkbox) {
                        $(checkbox).attr("checked", "checked");
                    });
                }
            }

            $scope.has_child = function (category) {
                var child = false;
                $.each($scope.categories, function (index, cat) {
                    if (cat.pid == category.cid) {
                        child = true;
                        return false;
                    }
                });
                return child;
            }

            $scope.save = function () {
                dfLoading.loading();

                $scope.yml.config = {};
                $('.b-list_main input:checked').each(function (index, checkbox) {
                    $scope.yml.config[$(checkbox).val()] = $(checkbox).val();
                });
                $http.post('[[link:admin_yml_data?action=save]]', {
                    yml: $scope.yml
                })
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }
                        dfNotice.ok('Данные сохранены');
                        $window.location.href = '[[link:admin_yml?action=edit]]?id=' + response.id;
                    });


            }

            $scope.selectAll = function () {
                $('.b-list_main input').each(function (index, checkbox) {
                    $(checkbox).attr("checked", "checked");
                });
            }

            $scope.unSelectAll = function () {
                $('.b-list_main input').each(function (index, checkbox) {
                    $(checkbox).removeAttr("checked", "checked");
                });
            }

        }])
    .controller('YmlListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.yml = [];

                $scope.init = function () {
                    $scope.yml = $window._yml;
                };


                $scope.clean = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_yml_data?action=clean]]').success(function (response) {
                        dfLoading.ready();
                        dfNotice.ok('YML удалены');
                    }).error(function () {
                        dfLoading.ready();
                        alert('Ошибка сервера');
                    });
                };


                $scope.deleteFile = function (id) {
                    dfLoading.loading();
                    $http.post('[[link:admin_yml_data?action=deleteFile]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.yml, function (index, yml) {
                                    if (yml.id == id) {
                                        $scope.yml[index].exist = false;
                                        return;
                                    }
                                });
                            }
                        });
                };


                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_yml_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.yml, function (index, yml) {
                                    if (yml.id == id) {
                                        $scope.yml.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };


                $scope.generation = function (id) {
                    dfLoading.loading();
                    $http.post('[[link:admin_yml_data?action=generation]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.yml, function (index, yml) {
                                    if (yml.id == id) {
                                        $scope.yml[index] = response.yml;
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        })
                        .error(function () {
                            dfLoading.ready();
                            alert('Ошибка сервера');
                        });
                };


            }])
    .controller('CollectionProductListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', 'dfImage',
            function ($scope, $window, dfNotice, dfLoading, $http, dfImage) {
                $scope.collections = [];
                $scope.form = {type_id: 1};

                $scope.init = function (type_id) {
                    $scope.form.type_id = type_id;
                    $scope.collections = $window._collections;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_product_collection_data?action=delete]]', {id: id});
                    $.each($scope.collections, function (index, item) {
                        if (item.id == id) {
                            $scope.collections.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.edit = function (collection) {
                    dfLoading.loading();
                    $http.post('[[link:admin_product_collection_data?action=save]]', {
                        collection: collection
                    })
                        .success(function (response) {
                            dfLoading.ready();
                        });
                };


                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_product_collection_data?action=save]]', {
                        collection: $scope.form
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $window.location.href = '[link:admin_product_collection?action=edit]?id=' + response.collection.id;
                            }

                        });
                };

            }])
    .controller('CollectionProductEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {
                $scope.collection = {};
                $scope.goods = [];
                $scope.items = [];
                $scope.goods = {};
                $scope.images = [];


                $scope.init = function () {
                    $scope.collection = $window._collection;
                    $scope.items = $window._items;
                    $scope.goods = $window._goods;
                    $scope.images = $window._images;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_product_collection_data?action=deleteItem]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            $.each($scope.items, function (index, item) {
                                if (item.id == id) {
                                    $scope.items.splice(index, 1);
                                    return false;
                                }
                            });
                        });
                }

                $scope.edit = function (item) {
                    $http.post('[[link:admin_product_collection_data?action=saveItem]]', {
                        item: item,
                        collection: $scope.collection
                    })
                        .success(function (response) {
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);

                                $.each($scope.items, function (index, item) {
                                    if (item.id == response.item.id) {
                                        $scope.items[index] = response.item;
                                    }
                                });
                            }
                        });
                };

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_product_collection_data?action=save]]', {
                        collection: $scope.collection
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.collection = response.collection;
                            }
                        });
                };


                $scope.getImageSrc = function (id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            src = image.preview;
                            return;
                        }
                    });
                    return src;
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.set(file, id,
                            '\\Shop\\Commodity\\Entity\\CollectionProductItem',
                            function (data, status, headers, config) {
                                i -= 1;
                                dfLoading.ready('image' + i);
                                if (data.error) {
                                    dfNotice.error(data.error);
                                }
                                else {
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.image_id == data.image_id) {
                                            $scope.images[index] = data;
                                            select = true;
                                        }
                                    });
                                    if (!select) {
                                        $scope.images.push(data);
                                    }
                                }
                            });
                    }
                };

                $scope.getNameProduct = function (item) {
                    var name = '';
                    $.each($scope.goods, function (index, goods) {
                        if (goods.goods_id == item.product_id) {
                            name = goods.name;
                            return;
                        }
                    });
                    return name;
                }

                $scope.$on('selectGoods', function (ev, goods) {
                    $scope.addGoods(goods);
                });

                $scope.addGoods = function (goods) {
                    var _bool = false;
                    $.each($scope.items, function (index, item) {
                        if (item.product_id == goods.goods_id) {
                            _bool = true;
                            return;
                        }
                    });
                    if (_bool) {
                        dfNotice.error('Данные товар уже добавлен');
                        return;
                    }
                    $scope.goods.push(goods);
                    dfLoading.loading();
                    var item = {product_id: goods.goods_id};
                    $http.post('[[link:admin_product_collection_data?action=saveItem]]', {
                        item: item,
                        collection: $scope.collection
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.items.push(response.item);
                            }
                        });
                };

            }])
    .controller('CategoryFilterListController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.filters = [];
                $scope.init = function (cid) {
                    $scope.select(cid);
                };


                $scope.select = function (cid) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_filter_data?action=get]]', {cid: cid})
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.filters = response.filters;
                        });
                };


                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[[link:admin_category_filter_data?action=status]]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.filters, function (index, item) {
                                    if (item.id == id) {
                                        $scope.filters[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                };


                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_category_filter_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.filters, function (index, item) {
                                    if (item.id == id) {
                                        $scope.filters.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                        });
                };


            }])
    .controller('CategoryFilterController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.filter = {};
                $scope.meta = null;
                $scope.ckeditor = [];


                $scope.init = function (cid) {
                    $scope.ckeditor['top'] = CKEDITOR.replace('text_top');
                    $scope.ckeditor['below'] = CKEDITOR.replace('text_below');

                    $scope.filter = $window._filter;
                    $scope.meta = $window._meta;
                }

                $scope.save = function () {
                    $scope.filter.text_top = $scope.ckeditor['top'].getData();
                    $scope.filter.text_below = $scope.ckeditor['below'].getData();

                    dfLoading.loading();

                    $http.post('[[link:admin_category_filter_data?action=save]]', {
                        filter: $scope.filter,
                        meta: $scope.meta
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                            }

                            if (response.ok) {
                                var href = '[[link:admin_category_filter?action=edit]]?id=' + response.id;
                                window.location.href = href;
                            }
                        });

                };


            }])
    .controller('LineListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.lines = [];

                $scope.init = function (cid) {
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=load]]')
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.lines = response.lines;
                        });
                };


                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=status]]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.lines, function (index, item) {
                                    if (item.id == id) {
                                        $scope.lines[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                };


                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.lines, function (index, item) {
                                    if (item.id == id) {
                                        $scope.lines.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                        });
                };

                $scope.add = function () {
                    if (!$scope.form.name) {
                        dfNotice.error('Укажите название');
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=save]]', {
                        line: $scope.form
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                var url = '[link:admin_line_product?action=edit]?id=' + response.line.id;
                                window.location.href = url;
                            }
                        });
                };


                $scope.save = function (line) {
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=save]]', {
                        line: line
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };


            }])
    .controller('LineEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {

                $scope.line = {};
                $scope.items = [];
                $scope.products = [];

                $scope.init = function () {
                    $scope.line = $window._line;
                    $scope.load();
                }


                $scope.$on('selectGoods', function (ev, goods) {
                    $scope.addGoods(goods);
                });


                $scope.getProduct = function (product_id) {
                    var product = {};
                    $.each($scope.products, function (index, item) {
                        if (item.goods_id == product_id) {
                            return product = item;
                        }
                    });

                    return product;
                }

                $scope.load = function () {
                    dfLoading.loading('load');
                    $http.post('[[link:admin_line_product_data?action=loadProductItem]]', {
                        id: $scope.line.id
                    })
                        .success(function (response) {
                            dfLoading.ready('load');

                            $scope.items = response.items;
                            $scope.products = response.products;

                        });
                }


                $scope.addGoods = function (goods) {
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=addProduct]]', {
                        goods_id: goods.goods_id,
                        line_id: $scope.line.id,
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.load();
                            }
                        });
                }


                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=save]]', {
                        line: $scope.line
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=deleteItem]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.items, function (index, item) {
                                    if (item.id == id) {
                                        $scope.items.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                        });
                };

                $scope.saveItem = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_line_product_data?action=saveItem]]', {
                        item: item
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.load();
                            }
                        });
                };

            }]);