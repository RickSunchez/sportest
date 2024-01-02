angular.module('df.app')
    .controller('CallbackController',
        ['$scope', '$http', 'dfLoading', 'dfNotice', '$timeout',
            function ($scope, $http, dfLoading, dfNotice, $timeout) {
                $scope.form = {};
                $scope.check = 1;
                $scope.timer = 27;
                $scope.timer_show = 1;
                $scope.timer_record = 27;

                $scope.send_part = function (subject, click) {
                    $scope.form.subject = subject;
                    if (click) {
                        __click = click;
                    }

                    if ($scope.check != 1) {
                        dfNotice.error('Вы не дали согласия на обработку персональных данных');
                        return;
                    }

                    if (!$scope.form.name) {
                        dfNotice.error('Укажите Ваше имя');
                        return;
                    }

                    if (!$scope.form.phone) {
                        dfNotice.error('Укажите Ваш телефон');
                        return;
                    }

                    if (!$scope.form.object) {
                        dfNotice.error('Укажите ремонтируемый объект');
                        return;
                    }

                    if (!$scope.form.part) {
                        dfNotice.error('Укажите данные о запчасти');
                        return;
                    }

                    if (dfLoading.is_loading('callback')) {
                        return;
                    }
                    dfLoading.loading('callback');
                    $http.post('[link:callback]', {form: $scope.form})
                        .success(function (response) {
                            dfLoading.ready('callback');
                            __reachGoal(__click);
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

                $scope.send = function (subject, click) {
                    $scope.form.subject = subject;
                    if (click) {
                        __click = click;
                    }

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
                    $http.post('[link:callback_realtime]', {form: $scope.form})
                        .success(function (response) {
                            dfLoading.ready('callback');

                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }

                            __reachGoal(__click);

                            if (response.realtime) {
                                var timer = function () {
                                    $timeout(function () {
                                        $scope.timer_record -= 1;
                                        $scope.timer_show = 0;
                                        if ($scope.timer_record == 0) {
                                            $scope.timer_record = $scope.timer;
                                            $scope.timer_show = 1;
                                            $scope.form = {};
                                        } else {
                                            $timeout(timer, 1000);
                                        }
                                    }, 1000);
                                };

                                timer();
                                return;
                            }

                            $scope.form = {};
                            $.magnificPopup.open({
                                items: {
                                    src: '<aside class="b-popup b-popup_ok" >' +
                                    '<p class="first">Спасибо, Ваша заявка принята.</p>' +
                                    '<p class="last">Мы свяжемся с вами в рабочее время</p>' +
                                    '</aside>',
                                    type: 'inline'
                                }
                            });
                        }).error(function () {
                        dfLoading.ready('callback');
                        dfNotice.error('Не удалось отправить сообщения, попробуйте позже.');
                    });
                }

                $scope.send_note = function (subject, click) {
                    $scope.form.subject = subject;
                    if (click) {
                        __click = click;
                    }

                    if ($scope.check != 1) {
                        dfNotice.error('Вы не дали согласия на обработку персональных данных');
                        return;
                    }

                    if (!$scope.form.email) {
                        dfNotice.error('Укажите Ваш e-mail');
                        return;
                    }

                    if (!$scope.form.note) {
                        dfNotice.error('Укажите Ваше сообщение');
                        return;
                    }
                    if (dfLoading.is_loading('callback')) {
                        return;
                    }
                    dfLoading.loading('callback');
                    $http.post('[link:callback]', {form: $scope.form})
                        .success(function (response) {
                            dfLoading.ready('callback');
                            __reachGoal(__click);
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

                $scope.send_order = function (subject, click) {
                    $scope.form.subject = subject;
                    if (click) {
                        __click = click;
                    }

                    $scope.form.part = schema_product;

                    if ($scope.check != 1) {
                        dfNotice.error('Вы не дали согласия на обработку персональных данных');
                        return;
                    }

                    if (!$scope.form.phone) {
                        dfNotice.error('Укажите Ваш телефон');
                        return;
                    }

                    if (!$scope.form.part) {
                        dfNotice.error('Укажите запчать');
                        return;
                    }


                    if (dfLoading.is_loading('callback')) {
                        return;
                    }
                    dfLoading.loading('callback');
                    $http.post('[link:callback]', {form: $scope.form})
                        .success(function (response) {
                            dfLoading.ready('callback');
                            __reachGoal(__click);
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

                $scope.send_order_mobile = function (subject, click) {
                    $scope.form.subject = subject;
                    if (click) {
                        __click = click;
                    }

                    $scope.form.part = schema_product;

                    if ($scope.check != 1) {
                        dfNotice.error('Вы не дали согласия на обработку персональных данных');
                        return;
                    }

                    if (!$scope.form.phone) {
                        dfNotice.error('Укажите Ваш телефон');
                        return;
                    }

                    if (!$scope.form.part) {
                        dfNotice.error('Укажите запчать');
                        return;
                    }


                    if (dfLoading.is_loading('callback')) {
                        return;
                    }
                    dfLoading.loading('callback');
                    $http.post('[link:callback]', {form: $scope.form})
                        .success(function (response) {
                            dfLoading.ready('callback');
                            if (__click)
                                __reachGoal(__click);
                            $scope.form = {};
                            $('.js-model').removeClass('js-model');
                            $('.js-model--active').removeClass('js-model--active');

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

