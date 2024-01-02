angular.module('df.app')
    .controller('CallbackController',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};
            $scope.check = 1;

            $scope.send = function (subject) {
                $scope.form.subject = subject;

                if ($scope.check != 1) {
                    dfNotice.error('Вы не дали согласия на обработку персональных данных');
                    return;
                }

                if (!$scope.form.phone) {
                    dfNotice.error('Укажите Ваш телефон');
                    return;
                }
                if (dfLoading.is_loading('callback')) {
                    return;
                }
                dfLoading.loading('callback');
                $http.post('[link:callback]', {form:$scope.form})
                    .success(function (response) {
                        dfLoading.ready('callback');
                        $scope.form = {};
                        $.magnificPopup.open({
                            items: {
                                src: '<aside class="b-popup b-popup_ok" >' +
                                '<p class="first">Спасибо, Ваша заявка принята.</p>' +
                                '<p class="last">Мы свяжемся с вами в ближайшее время</p>' +
                                '</aside>',
                                type: 'inline'
                            }
                        });
                    }).error(function () {
                        dfLoading.ready('callback');
                        dfNotice.error('Не удалось отправить сообщения, попробуйте позже.');
                    });
            }
        }])
    .controller('ReviewController',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};

            $scope.send = function (subject) {
                $scope.form.subject = subject;
                if (!$scope.form.author) {
                    dfNotice.error('Укажите Ваше имя');
                    return;
                }
                if (!$scope.form.text) {
                    dfNotice.error('Укажите Ваш отзыв');
                    return;
                }
                dfLoading.loading();
                $http.post('[link:review_add_data]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.form = {};
                        $.magnificPopup.close();
                        dfNotice.ok('Спасибо за обращения, Ваш отзыв принял');
                    }).error(function () {
                        dfLoading.ready();
                        dfNotice.error('Не удалось отправить сообщения, попробуйте позже.');
                    });
            }
        }])
    .controller('QuestionController',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};

            $scope.send = function () {
                if (!$scope.form.name) {
                    dfNotice.error('Укажите Ваше имя');
                    return;
                }
                if (!$scope.form.contact) {
                    dfNotice.error('Укажите Ваш контакт куда мы можем Вам ответить');
                    return;
                }
                if (!$scope.form.text) {
                    dfNotice.error('Укажите Ваш вопрос');
                    return;
                }

                if (dfLoading.is_loading('question')) {
                    return;
                }
                dfLoading.loading('question');
                $http.post('[link:question_add_data]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready('question');
                        $scope.form = {};
                        $.magnificPopup.close();
                        dfNotice.ok('Спасибо за обращения, Ваш вопрос на  рассмотрении');
                    }).error(function () {
                        dfLoading.ready('question');
                        dfNotice.error('Не удалось отправить сообщения, попробуйте позже.');
                    });
            }
        }]);

