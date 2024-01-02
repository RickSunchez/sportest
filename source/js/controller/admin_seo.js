angular.module('df.admin')
    .controller('TemplateListCtr',
    ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
        function ($scope, $http, dfLoading, $window, dfNotice) {
            $scope.templates = [];

            $scope.init = function () {
                $scope.templates = $window._templates;
            }

            $scope.delete = function (id) {
                if (!confirm('Вы действительно хотите удалить?')) {
                    return false;
                }
                dfLoading.loading();
                $http.post('[link:admin_tmp_data?action=delete]', {id: id})
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            alert(response.error);
                            return;
                        }
                        if (response.ok) {
                            $.each($scope.templates, function (index, template) {
                                if (template.id == id) {
                                    $scope.templates.splice(index, 1);
                                    return false;
                                }
                            });
                            dfNotice.ok(response.ok);
                            return;
                        }
                    });
            };

        }])
    .controller('TemplateController',
    ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
        function ($scope, $http, dfLoading, $window, dfNotice) {
            $scope.tmp = {};
            $scope.text = null;
            $scope.count = null;

            $scope.init = function () {
                $scope.tmp = $window._tmp;
                $scope.count = $window._count;
            }


            $scope.save = function () {

                dfLoading.loading();
                $http.post('[link:admin_tmp_data?action=save]', {
                    tmp: $scope.tmp
                })
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }

                        if (response.ok) {
                            if ($scope.tmp.id) {
                                dfNotice.ok(response.ok);
                            } else {
                                var href = '[link:admin_tmp?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }

                        }
                    });

            };

            $scope.getAll = function (tmp) {
                if (!confirm('Вы уверены что хотите перегенерировать шаблоны?')) {
                    return false;
                }
                dfLoading.loading();
                $http.post('[link:admin_tmp_data?action=generate]', {
                    tmp: tmp
                }).success(function (response) {
                    dfLoading.ready();
                    $scope.count = response.count;
                });
            }

            $scope.example = function (tmp) {
                dfLoading.loading();
                $http.post('[link:admin_tmp_data?action=example]', {
                    tmp: tmp
                }).success(function (response) {
                    dfLoading.ready();

                    $scope.text = response.text;
                });
            }

        }]).controller('SearchListCtr',
    ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
        function ($scope, $http, dfLoading, $window, dfNotice) {
            $scope.search = [];

            $scope.init = function () {
                $scope.search = $window._search;
            }

        }]);