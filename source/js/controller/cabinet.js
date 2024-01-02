angular.module('df.app')
    .controller('LoginController',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};
            $scope.error = null;
            $scope.errors = null;

            $scope.login = function () {
                if (dfLoading.is_loading('login')) {
                    return;
                }
                dfLoading.loading('login');
                $http.post('[[link:user_login_data?action=auth]]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready('login');
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {
                            window.location.reload();
                        }
                    });
            }
            $scope.reg = function () {
                if (dfLoading.is_loading('reg')) {
                    return;
                }
                dfLoading.loading('reg');
                $http.post('[[link:user_login_data?action=reg]]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready('reg');
                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }
                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            window.location.reload();
                        }
                    });
            }
            $scope.forgot = function () {
                if (dfLoading.is_loading('forgot')) {
                    return;
                }
                dfLoading.loading('forgot');
                $http.post('[[link:user_login_data?action=forgot]]', {email: $scope.form.email})
                    .success(function (response) {
                        dfLoading.ready('forgot');
                        if (response.error) {
                            $scope.error = response.error;
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            $.magnificPopup.close();
                        }
                    });
            };
        }])
    .controller('CabinetCtrl', ['$scope', '$http', 'dfLoading', function ($scope, $http, dfLoading) {
    }])
    .controller('AuthCtrl',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};

            $scope.login = function () {
                dfLoading.loading();
                $http.post('[[link:user_login_data?action=auth]]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {
                            window.location.reload();
                        }
                    });
            }

        }])
    .controller('RegCtrl',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};
            $scope.reg = function () {
                dfLoading.loading();
                $http.post('[[link:user_login_data?action=reg]]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }
                        if (response.ok) {
                            window.location.reload();
                        }
                    });
            }

        }])
    .controller('RemindCtrl',
    ['$scope', '$window', '$http', 'dfNotice', 'dfLoading',
        function ($scope, $window, $http, dfNotice, dfLoading) {
            $scope.form = {};
            $scope.remind = function () {
                if (typeof $scope.form.email == 'undefined') {
                    dfNotice.error('Неверно указан E-mail');
                    return;
                }
                dfLoading.loading();
                $http.post('[[link:user_login_data?action=forgot]]', {email: $scope.form.email})
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }

                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            $scope.form = {};
                        }
                    })
                    .error(function () {
                        dfLoading.ready();
                    });

            };
        }])
    .controller('UserEditCtrl',
    ['$scope', '$http', 'dfLoading', 'dfNotice', '$window', '$upload',
        function ($scope, $http, dfLoading, dfNotice, $window, $upload) {
            $scope.user = {};
            $scope.image = null;
            $scope.attr_value = [];
            $scope.user_attrs = [];
            $scope.attr_name = [];

            $scope.init = function () {
                $scope.user = $window._user;
                $scope.image = $window._image;
                $scope.user_attrs = $window._user_attrs;
                $scope.attr_name = $window._attr_name;
                $.each($scope.attr_name, function (index, item) {
                    $scope.addAttr(item);
                });
            }

            $scope.addAttr = function (attr) {
                var user_attr = $scope.user_attrs[attr.id] || {attr_id: attr.id, group_id: attr.group_id, value: ""};
                user_attr['name'] = attr.name;
                user_attr['require'] = attr.require;
                user_attr['code'] = attr.code;
                $scope.attr_value.push(user_attr);
            }

            $scope.save = function () {
                if ($scope.user.newPassword && $scope.user.newPassword != $scope.user.newPasswordVerify) {
                    dfNotice.error('Укажите новый пароль');
                    return;
                }
                var user_attr = [];
                var error = false;
                $.each($scope.attr_value, function (index, attr) {
                    if (attr.require == 1 && attr.value.length == 0) {
                        dfNotice.error('Поле "' + attr.name + '" обезательно к заполнению');
                        error = true;
                        return;
                    }
                    user_attr.push(attr);
                });
                if (error) {
                    return;
                }
                dfLoading.loading();
                $http.post('[[link:cabinet_user_data?action=edit]]', {user: $scope.user, attr: user_attr})
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }
                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            $scope.user_attrs = response.user_attrs;
                            $scope.attr_value = [];
                            $.each($scope.attr_name, function (index, item) {
                                $scope.addAttr(item);
                            });
                        }
                    });
            }


            $scope.onFileSelect = function ($files, user) {

                for (var i = 0; i < $files.length; i++) {
                    var file = $files[i];
                    dfLoading.loading();
                    $scope.upload = $upload.upload({
                        url: '[[link:cabinet_user_data?action=upload]]',
                        data: {user: user},
                        file: file
                    }).progress(function (evt) {
                        console.log(evt);
                        console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
                    }).success(function (data, status, headers, config) {
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
    .controller('PrivateMessage', ['$scope', '$http', '$window', function ($scope, $http, $window) {
        $scope.text = '';
        $scope.empty = '';
        $scope.clear_button = false;
        $scope.empty_message = 'Сообщений нет';

        $scope.save = function () {
            if ($scope.text.length > 0)
                $http.post('[[link:im_data?action=addMessage]]', {text: $scope.text, user: $scope.user})
                    .success(function (response) {
                        if (response.ok && response.message) {
                            $scope.messages.splice(0, 0, response.message);
                            $scope.empty = $scope.text = '';
                            $scope.clear_button = true;
                        }
                        if (response.error)
                            alert(response.error);
                    });
        };

        $scope.init = function () {
            $scope.user = $window._userPM;
            $scope.messages = $window._messagesPM.reverse();
            if ($window._messagesPM.length > 0)
                $scope.clear_button = true;
            else
                $scope.empty = $scope.empty_message;
            $scope.users = $window._usersPM;
            $scope.user = $window._userPM;
        };

        $scope.clearDialog = function () {
            $http.post('[[link:im_clear_dialog]]' + $scope.user.user_id)
                .success(function () {
                    $scope.messages = [];
                    $scope.empty = $scope.empty_message;
                    $scope.clear_button = false
                });
        };
    }])
    .controller('HelpDeskCtrl',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};

            $scope.send = function () {
                if (!$scope.form.subject) {
                    dfNotice.error('Укажите заголовок');
                    return;
                }
                if (!$scope.form.text) {
                    dfNotice.error('Напишите Ваш комментарий');
                    return;
                }
                dfLoading.loading();
                $http.post('[[link:help_desk_im_data?action=add]]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.form = {};

                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            $scope.form = {};
                            window.location.reload();
                        }

                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }


                    }).error(function () {
                        dfLoading.ready();
                        dfNotice.error('Не удалось отправить сообщения, попробуйте позже.');
                    });
            }

        }])
    .controller('HelpDeskMessageCtrl',
    ['$scope', '$http', 'dfLoading', 'dfNotice', '$window',
        function ($scope, $http, dfLoading, dfNotice, $window) {
            $scope.form = {};
            $scope.task = {};

            $scope.init = function () {
                $scope.task = $window._task;
            }

            $scope.send = function () {
                if (!$scope.form.text) {
                    dfNotice.error('Напишите Ваш комментарий');
                    return;
                }
                dfLoading.loading();
                $http.post('[[link:help_desk_im_data?action=addMassage]]', {
                    form: $scope.form,
                    task_id: $scope.task.task_id
                })
                    .success(function (response) {
                        dfLoading.ready();
                        $scope.form = {};

                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            window.location.reload();

                        }

                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }


                    }).error(function () {
                        dfLoading.ready();
                        dfNotice.error('Не удалось отправить сообщения, попробуйте позже.');
                    });
            }

        }])
    .controller('HelpDeskListCtrl',
    ['$scope', '$http', 'dfLoading', 'dfNotice',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {};

            $scope.init = function () {
                $scope.tasks = window._tasks;
            }

        }])
    .controller('HelpDeskMsgListCtrl',
    ['$scope', '$http', 'dfLoading', 'dfNotice', '$sce',
        function ($scope, $http, dfLoading, dfNotice, $sce) {
            $scope.form = {};

            $scope.init = function () {
                $scope.msgs = window._messages;
                $scope.task = window._task;
            }

            $scope.as_html = function (html) {
                return $sce.trustAsHtml(html);
            }

        }])
    .controller('UserBillController',
    ['$scope', '$http', 'dfLoading', 'dfNotice', '$sce',
        function ($scope, $http, dfLoading, dfNotice) {
            $scope.form = {payment_id: 1};


            $scope.send = function () {
                if (!$scope.form.value) {
                    dfNotice.error('Напишите сумму');
                    return;
                }
                dfLoading.loading();
                $http.post('[[link:cabinet_account_data]]', $scope.form)
                    .success(function (response) {
                        dfLoading.ready();
                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            window.location.reload();
                        }
                        if (response.errors) {
                            dfNotice.errors(response.errors);
                            return;
                        }

                    }).error(function () {
                        dfLoading.ready();
                        dfNotice.error('Не удалось отправить сообщения, попробуйте позже.');
                    });
            }

        }]);


$(function () {
    $('.open-popup').magnificPopup({
        type: 'inline',
        midClick: true
    });
});
