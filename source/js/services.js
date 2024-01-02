angular.module('df.common')
    .filter('truncate', function () {
        return function (name, length) {
            length = length || 20;
            if (name.length > length) {
                return name.substring(0, length) + ' ...';
            } else {
                return name;
            }
        }
    })
    .filter('to_html', ['$sce', function ($sce) {
        return function (input) {
            if (angular.isUndefined(input) || input === null || input === '') {
                return '';
            } else {
                return $sce.trustAsHtml(input);
            }

        };
    }])
    .filter('ifEmptySelect', ['$sce', function ($sce) {
        return function (input, select) {
            if (angular.isUndefined(input) || input === null || input === '') {
                var defaultValue = $(select).html();
                if (defaultValue != null)
                    return $sce.trustAsHtml(defaultValue.toString());
            }
            if (input != null)
                return $sce.trustAsHtml(input.toString());

        };
    }])
    .filter('ifEmpty', function () {
        return function (input, defaultValue) {
            if (angular.isUndefined(input) || input === null || input === '') {
                return defaultValue;
            }
            return input;
        }
    })
    .factory('dfLoading', function ($rootScope) {
        return {
            loading: function (target) {
                df.loading(target);
            },
            is_loading: function (target) {
                return df.is_loading(target);
            },
            ready: function (target) {
                df.ready(target);
            },
            show: function (select, target) {
                df.loading_show(select, target);
            },
            hide: function (select, target) {
                df.loading_hide(select, target);
            }
        };
    })
    .factory('dfModal', function () {

        return {
            openAjax: function (url) {
                $.magnificPopup.open({
                    items: {
                        src: url
                    },
                    type: 'ajax',
                    callbacks: {
                        parseAjax: function (mfpResponse) {
                            mfpResponse.data = mfpResponse.xhr.responseJSON.html;
                        },
                        ajaxContentAdded: function () {
                            var $div = this.content;
                            angular.element(document).injector().invoke(function ($compile) {
                                var scope = angular.element($div).scope();
                                $compile($div)(scope);
                            });
                        }
                    }
                });
            },
            close: function () {
                $.magnificPopup.close();
            }
        }
    })
    .factory('dfNotice', function () {

        var options = {
            closer: false,
            sticker: false,
            styling: 'bootstrap3'
        };


        function confirm_dialog(title, text, callback, buttons) {
            if (typeof buttons !== "object") {
                buttons = {
                    "Да": true,
                    "Нет": false
                }
            }
            var notice;
            text = $('<div>' + text + '<br style="clear: both;" /><div class="button-container" style="margin-top: 10px; text-align: right;"></div></div>');
            $.each(buttons, function (b, val) {
                text.find("div.button-container").append($('<button style="margin-left: 5px;" class="btn btn-default btn-small">' + b + '</button>').click(function () {
                    notice.pnotify_remove();
                    callback.call(notice, val);
                }));
            });
            opts = {
                title: title,
                text: text,
                insert_brs: false,
                closer: false,
                sticker: false,
                hide: false,
                history: false,
                icon: 'glyphicon glyphicon-question-sign'
            };
            angular.extend(options, opts);
            notice = $.pnotify(options);
        }

        return {
            notice: function (opts) {
                angular.extend(options, opts);
                var notice = $.pnotify(options).click(function () {
                    notice.pnotify_remove();
                });

            },
            error: function (text, title) {
                title = title || 'Ошибка';
                this.notice({text: text, title: title, type: 'error'});
            },
            errors: function (errors, title) {
                title = title || 'Ошибка';
                var html = '<ol>';
                $.each(errors, function (index, error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ol>';
                this.notice({text: html, title: title, type: 'error'});
            },
            ok: function (text, title) {
                title = title || 'Готово';
                this.notice({text: text, title: title, type: 'success'})
            },
            info: function (text, title) {
                title = title || 'Внимание';
                this.notice({text: text, title: title, type: 'info'})
            },
            confirm: function (title, text, callback, buttons) {
                confirm_dialog(title, text, callback, buttons);
            }
        }
    })
    .factory('dfImage', function ($http, $upload) {

        function _progress(evt) {
        };

        function _error(data) {
            alert('Ошибка на сервере');
        };

        return {
            refresh: function (id, callback) {
                callback = callback || function () {
                };
                $http.post('[[link:admin_image_data?action=refresh]]', {id: id})
                    .success(callback)
                    .error(_error);
            },
            crop: function (id, coords, callback) {
                callback = callback || function () {
                };
                $http.post('[[link:admin_image_data?action=crop]]', {id: id, coords: coords})
                    .success(callback)
                    .error(_error);
            },
            add: function (file, id, _class, callback) {
                if (callback == null) {
                    alert('Укажите callback для dfImage.add');
                    return;
                }
                if (_class == null) {
                    alert('Укажите class для dfImage.add');
                    return;
                }
                if (id == null) {
                    alert('Укажите ID для dfImage.add');
                    return;
                }
                var upload = $upload.upload({
                    url: '[[link:admin_image_data?action=add]]',
                    data: {id: id, class: _class},
                    file: file
                }).progress(_progress).success(callback);
            },
            set: function (file, id, _class, callback) {
                if (callback == null) {
                    alert('Укажите callback для dfImage.set');
                    return;
                }
                if (_class == null) {
                    alert('Укажите class для dfImage.set');
                    return;
                }
                if (id == null) {
                    alert('Укажите ID для dfImage.set');
                    return;
                }
                var upload = $upload.upload({
                    url: '[[link:admin_image_data?action=set]]',
                    data: {id: id, class: _class},
                    file: file
                }).progress(_progress).success(callback);
            },
            update: function (file, id, callback) {
                if (callback == null) {
                    alert('Укажите callback для dfImage.update');
                    return;
                }

                if (id == null) {
                    alert('Укажите ID для dfImage.update');
                    return;
                }
                var upload = $upload.upload({
                    url: '[[link:admin_image_data?action=new]]',
                    data: {id: id},
                    file: file
                }).progress(_progress).success(callback);
            },
            main: function (id, status, callback) {
                if (id == null) {
                    alert('Укажите ID для dfImage.main');
                    return;
                }
                callback = callback || function () {
                };
                $http.post('[[link:admin_image_data?action=main]]', {id: id, status: status})
                    .success(callback)
                    .error(_error);
            },
            delete: function (id, callback) {
                if (id == null) {
                    alert('Укажите ID для dfImage.delete');
                    return;
                }
                callback = callback || function () {
                };
                $http.post('[[link:admin_image_data?action=delete]]', {id: id})
                    .success(callback)
                    .error(_error);
            },
            save: function (image, callback) {
                if (image == null) {
                    alert('Укажите image для dfImage.save');
                    return;
                }
                callback = callback || function () {
                };
                $http.post('[[link:admin_image_data?action=save]]', image)
                    .success(callback)
                    .error(_error);
            },
            priority: function (id, priority, type, callback) {
                if (id == null) {
                    alert('Укажите ID для dfImage.priority');
                    return;
                }
                priority = priority || 0;
                type = type || 'edit';
                $http.post('[[link:admin_image_data?action=pos]]', {id: id, pos: priority, type: type})
                    .success(callback);
            }
        };
    })
    .factory('dfFile', function ($http, $upload) {

        function _progress(evt) {
        };

        function _error(data) {
            alert('Ошибка на сервере');
        };

        return {
            add: function (file, id, _class, callback) {
                if (callback == null) {
                    alert('Укажите callback для dfFile.add');
                    return;
                }
                if (_class == null) {
                    alert('Укажите class для dfFile.add');
                    return;
                }
                if (id == null) {
                    alert('Укажите ID для dfFile.add');
                    return;
                }
                var upload = $upload.upload({
                    url: '[[link:admin_file_data?action=add]]',
                    data: {id: id, class: _class},
                    file: file
                }).progress(_progress).success(callback);
            },
            set: function (file, id, _class, callback) {
                if (callback == null) {
                    alert('Укажите callback для dfFile.set');
                    return;
                }
                if (_class == null) {
                    alert('Укажите class для dfFile.set');
                    return;
                }
                if (id == null) {
                    alert('Укажите ID для dfFile.set');
                    return;
                }
                var upload = $upload.upload({
                    url: '[[link:admin_file_data?action=set]]',
                    data: {id: id, class: _class},
                    file: file
                }).progress(_progress).success(callback);
            },
            delete: function (id, callback) {
                if (id == null) {
                    alert('Укажите ID для dfFile.delete');
                    return;
                }
                callback = callback || function () {
                };
                $http.post('[[link:admin_file_data?action=delete]]', {id: id})
                    .success(callback)
                    .error(_error);
            },
            save: function (image, callback) {
                if (image == null) {
                    alert('Укажите image для dfFile.save');
                    return;
                }
                callback = callback || function () {
                };
                $http.post('[[link:admin_file_data?action=save]]', image)
                    .success(callback)
                    .error(_error);
            }
        };
    })
    .factory('dfComment', function ($http) {

        function _error(data) {
            alert('Ошибка на сервере');
        };

        return {
            delete: function (id, callback) {
                if (id == null) {
                    alert('Укажите ID для dfComment.delete');
                    return;
                }
                callback = callback || function () {
                };
                $http.post('[[link:admin_comment_data?action=delete]]', {id: id})
                    .success(callback)
                    .error(_error);
            },
            save: function (comment, id, _class, callback) {
                if (comment == null) {
                    alert('Укажите comment для dfComment.save');
                    return;
                }
                callback = callback || function () {
                };
                $http.post('[[link:admin_comment_data?action=save]]', {comment: comment, id: id, class: _class})
                    .success(callback)
                    .error(_error);
            }
        };
    });

