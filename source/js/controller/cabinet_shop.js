angular.module('df.app')
    .controller('CabinetOrdersListCtrl',
        ['$scope', '$http', 'dfLoading', 'dfNotice', '$sce',
            function ($scope, $http, dfLoading, dfNotice, $sce) {
                $scope.form = [];
                $scope.orders = [];

                $scope.init = function () {
                    $scope.orders = window._orders;
                }

                $scope.as_html = function(html){
                    return $sce.trustAsHtml(html);
                }
            }]);