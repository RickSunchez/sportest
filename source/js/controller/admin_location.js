angular.module('df.admin')
    .controller('CountriesListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http', 'dfImage',
            function ($scope, $window, dfNotice, dfLoading, $http, dfImage) {
                $scope.countries = [];
                $scope.images = [];
                $scope.form = {};

                $scope.init = function () {
                    $scope.countries = $window._countries;
                    $scope.images = $window._images;
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

                $scope.edit = function (item) {
                    $http.post('[[link:admin_country_data?action=save]]', item)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok)
                            }
                            $scope.countries = response.countries;
                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_country_data?action=delete]]', {id: id});
                    $.each($scope.countries, function (index, country) {
                        if (country.id == id) {
                            $scope.countries.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_country_data?action=save]]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {};
                            }
                            $scope.countries = response.countries;
                        });
                };

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.set(file, id,
                            '\\Location\\Core\\Entity\\Country',
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
    .controller('CitiesControllers',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.cities = [];

                $scope.init = function () {
                    $scope.cities = $window._cities;
                }

                $scope.main = function (id, main) {
                    dfLoading.loading();
                    $http.post('[[link:admin_city_data?action=main]]', {id: id, main: main})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.cities, function (index, city) {
                                    if (city.id == id) {
                                        $scope.cities[index].main = main;
                                    } else {
                                        $scope.cities[index].main = 0;
                                    }
                                });
                            }

                        });
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[[link:admin_city_data?action=status]]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.cities, function (index, city) {
                                    if (city.id == id) {
                                        $scope.cities[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }

                $scope.change_pos = function (item) {
                    dfLoading.loading();
                    $http.post('[[link:admin_city_data?action=save]]', {
                        city: item
                    })
                        .success(function (response) {
                            dfLoading.ready();
                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[[link:admin_city_data?action=delete]]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.cities, function (index, city) {
                                    if (city.id == id) {
                                        $scope.cities.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }


            }])
    .controller('CityController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.city = {};
                $scope.form = {};
                $scope.image = null;
                $scope.fields = {};
                $scope.options = [];
                $scope.countries = [];
                $scope.metro = [];

                $scope.init = function () {
                    $scope.fields = $window._fields;
                    $scope.image = $window._image;
                    $scope.options = $window._options;
                    $scope.countries = $window._countries;
                    $scope.metro = $window._metro;
                    $scope.city = $window._city;
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_city_data?action=save]]', {
                        city: $scope.city,
                        meta: $scope.meta,
                        options: $scope.options
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                var href = '[[link:admin_city?action=edit]]?id=' + response.id;
                                window.location.href = href;
                            }
                        });
                }
                $scope.getNameField = function (code) {
                    if (df.hasObjectKey($scope.fields, code)) {
                        if (typeof $scope.fields[code] == 'string') {
                            return $scope.fields[code];
                        } else {
                            return $scope.fields[code][0];
                        }
                    }

                    return code;
                }

                $scope.getLabelField = function (code) {
                    if (df.hasObjectKey($scope.fields, code)) {
                        if (typeof $scope.fields[code] == 'string') {
                            return false;
                        } else {
                            return $scope.fields[code][1];
                        }
                    }
                    return 'Нет поля';
                }


                $scope.addMetro = function () {
                    dfLoading.loading();
                    $scope.form.city_id = $scope.city.id;
                    $scope.form.status = 1;
                    $scope.form.pos = 0;
                    $http.post('[[link:admin_city_data?action=saveMetro]]', {metro: $scope.form})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {};
                                temp = angular.copy($scope.metro);
                                $scope.metro = [];
                                $scope.metro.push(response.metro);
                                $.each(temp, function (index, item) {
                                    $scope.metro.push(item);
                                });
                            }
                        });
                }

                $scope.saveMetro = function (metro) {
                    $http.post('[[link:admin_city_data?action=saveMetro]]', {metro: metro})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $.each($scope.metro, function (index, item) {
                                    if (item.id == response.metro.id) {
                                        $scope.metro[index] = response.metro;
                                        return false;
                                    }
                                });
                            }
                        });
                }

                $scope.deleteMetro = function (metro) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_city_data?action=deleteMetro]]', {id: metro.id});
                    $.each($scope.metro, function (index, item) {
                        if (item.id == metro.id) {
                            $scope.metro.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\Location\\Core\\Entity\\City',
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

            }]);
