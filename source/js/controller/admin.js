angular.module('df.admin')
    .controller('AdminCtrl',
        ['$scope', '$http', 'dfLoading', 'dfNotice',
            function ($scope, $http, dfLoading, dfNotice) {

                $scope.clearCache = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_cache_clean]').success(function (response) {
                        dfLoading.ready();
                        dfNotice.ok('Кеш файлов очищен');
                    }).error(function () {
                        dfLoading.ready();
                        alert('Ошибка сервера');
                    });
                }

                $scope.clearCacheThumb = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_cache_clean_thumb]').success(function (response) {
                        dfLoading.ready();
                        dfNotice.ok('Кеш картинок очищен');
                    }).error(function () {
                        dfLoading.ready();
                        alert('Ошибка сервера');
                    });
                }

                $scope.clearCacheTheme = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_cache_clean_theme]').success(function (response) {
                        dfLoading.ready();
                        dfNotice.ok('Кеш css|js очищен');
                    }).error(function () {
                        dfLoading.ready();
                        alert('Ошибка сервера');
                    });
                }

                $scope.createSitemaps = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_sitemaps_create]').success(function (response) {
                        dfLoading.ready();
                        dfNotice.ok('Sitemaps.xml созданы');
                    }).error(function () {
                        dfLoading.ready();
                        alert('Ошибка сервера');
                    });
                }

                $scope.clearSitemaps = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_sitemaps_clear]').success(function (response) {
                        dfLoading.ready();
                        dfNotice.ok('Sitemaps.xml удалены');
                    }).error(function () {
                        dfLoading.ready();
                        alert('Ошибка сервера');
                    });
                }

            }])
    .controller('LoginController',
        ['$scope', '$http', 'dfLoading',
            function ($scope, $http, dfLoading) {
                $scope.form = {};

                $scope.login = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_login_data]', $scope.form).success(function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            alert(response.error);
                            return;
                        }
                        if (response.ok) {
                            window.location.reload();
                        }
                    });
                }

            }])
    .controller('RobotsController',
        ['$scope', '$http', 'dfLoading', 'dfNotice', '$window',
            function ($scope, $http, dfLoading, dfNotice, $window) {
                $scope.domain = {};
                $scope.robots = {};
                $scope.form = {};
                $scope.select_domain = "";
                $scope.select = 0;

                $scope.init = function () {
                    $scope.domain = $window._domain;
                    $scope.robots = $window._robots;
                }


                $scope.selected = function () {
                    if ($scope.select_domain) {
                        var _isset = false;
                        $.each($scope.robots, function (index, robots) {
                            if (robots.domain == $scope.select_domain) {
                                $scope.form = robots;
                                _isset = true;
                                return true;
                            }
                        });
                        if (!_isset) {
                            $scope.form = {domain: $scope.select_domain}
                        }
                        $scope.select = 1;
                    } else {
                        $scope.select = 0;
                    }
                };

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_robots_data?action=save]', $scope.form).success(function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {
                            dfNotice.ok(response.ok);
                            var isset = false;
                            $scope.form = response.robots;
                            $.each($scope.robots, function (index, robots) {
                                if (robots.robots_id == response.robots.robots_id) {
                                    isset = true;
                                    $scope.robots[index] = response.robots;
                                    return;
                                }
                            });
                            if (!isset) {
                                $scope.robots.push(response.robots);
                            }
                        }
                    });
                }

            }])
    .controller('AnalyticsController',
        ['$scope', '$http', 'dfLoading', 'dfNotice', '$window',
            function ($scope, $http, dfLoading, dfNotice, $window) {
                $scope.domain = {};
                $scope.analytics = {};
                $scope.form = {};
                $scope.select_domain = "";
                $scope.select = 0;

                $scope.init = function () {
                    $scope.domain = $window._domain;
                    $scope.analytics = $window._analytics;
                }


                $scope.selected = function () {
                    if ($scope.select_domain) {
                        var _isset = false;
                        $.each($scope.analytics, function (index, analytics) {
                            if (analytics.domain == $scope.select_domain) {
                                $scope.form = analytics;
                                _isset = true;
                                return true;
                            }
                        });
                        if (!_isset) {
                            $scope.form = {domain: $scope.select_domain}
                        }
                        $scope.select = 1;
                    } else {
                        $scope.select = 0;
                    }
                };

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_analytics_data?action=save]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                var isset = false;
                                $scope.form = response.analytics;
                                $.each($scope.analytics, function (index, analytics) {
                                    if (analytics.domain == response.analytics.domain) {
                                        isset = true;
                                        $scope.analytics[index] = response.analytics;
                                        return;
                                    }
                                });
                                if (!isset) {
                                    $scope.analytics.push(response.analytics);
                                }

                            }
                        });
                }

            }])
    .controller('NewsListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.news = [];
                $scope.categories = [];
                $scope.domain = {};
                $scope.select_domain = 'www';

                $scope.init = function () {
                    $scope.select_domain = $window._select_domain;
                    $scope.news = $window._news;
                    $scope.domain = $window._domain;
                    $scope.categories = $window._categories
                }

                $scope.select = function () {
                    $window.location.href = '?domain=' + $scope.select_domain;
                }

                $scope.getNameCat = function (cid) {
                    var name = '';
                    $.each($scope.categories, function (index, item) {
                        if (item.value == cid) {
                            name = item.name;
                        }
                    });

                    return name;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.main = function (id, main) {
                    dfLoading.loading();
                    $http.post('[link:admin_news_data?action=main]', {id: id, main: main})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.news, function (index, news) {
                                    if (news.id == id) {
                                        $scope.news[index].main = main;
                                        return;
                                    }
                                });
                            }

                        });
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_news_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.news, function (index, news) {
                                    if (news.id == id) {
                                        $scope.news[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_news_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.news, function (index, news) {
                                    if (news.id == id) {
                                        $scope.news.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }


            }])
    .controller('NewsController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.news = {};
                $scope.image = null;
                $scope.meta = {};
                $scope.categories = [];
                $scope.tags = [];
                $scope.domain = {};

                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('text');

                    $scope.categories = $window._categories;
                    $scope.news = $window._news;
                    $scope.meta = $window._meta;
                    $scope.image = $window._image;
                    $scope.tags = $window._tags;
                    $scope.domain = $window._domain;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.save = function () {
                    $scope.news.text = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_news_data?action=save]', {
                        news: $scope.news,
                        meta: $scope.meta,
                        tags: $scope.tags
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                var href = '[link:admin_news?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\News',
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
    .controller('EventListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.events = [];
                $scope.categories = [];
                $scope.domain = {};
                $scope.select_domain = 'www';

                $scope.init = function () {
                    $scope.events = $window._events;
                    $scope.domain = $window._domain;
                    $scope.select_domain = $window._select_domain;
                    $scope.categories = $window._categories
                }

                $scope.select = function () {
                    $window.location.href = '?domain=' + $scope.select_domain;
                }


                $scope.getNameCat = function (cid) {
                    var name = '';
                    $.each($scope.categories, function (index, item) {
                        if (item.value == cid) {
                            name = item.name;
                        }
                    });

                    return name;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.main = function (id, main) {
                    dfLoading.loading();
                    $http.post('[link:admin_event_data?action=main]', {id: id, main: main})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.events, function (index, event) {
                                    if (event.id == id) {
                                        $scope.events[index].main = main;
                                        return;
                                    }
                                });
                            }

                        });
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_event_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.events, function (index, event) {
                                    if (event.id == id) {
                                        $scope.events[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_event_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.events, function (index, event) {
                                    if (event.id == id) {
                                        $scope.events.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }


            }])
    .controller('EventController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.event = {};
                $scope.image = null;
                $scope.meta = {};
                $scope.categories = [];
                $scope.domain = {};

                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('text');
                    $scope.categories = $window._categories;
                    $scope.event = $window._event;
                    $scope.meta = $window._meta;
                    $scope.image = $window._image;
                    $scope.domain = $window._domain;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.save = function () {
                    $scope.event.text = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_event_data?action=save]', {
                        event: $scope.event,
                        meta: $scope.meta

                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                var href = '[link:admin_event?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\Event',
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
    .controller('ArticleListCtr',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.articles = [];
                $scope.categories = [];
                $scope.domain = {};
                $scope.select_domain = 'www';

                $scope.init = function () {
                    $scope.select_domain = $window._select_domain;
                    $scope.articles = $window._articles;
                    $scope.categories = $window._categories;
                    $scope.domain = $window._domain;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_article_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.articles, function (index, article) {
                                    if (article.id == id) {
                                        $scope.articles.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_article_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.articles, function (index, article) {
                                    if (article.id == id) {
                                        $scope.articles[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.getNameCat = function (cid) {
                    var name = '';
                    $.each($scope.categories, function (index, item) {
                        if (item.value == cid) {
                            name = item.name;
                        }
                    });

                    return name;
                }

                $scope.select = function () {
                    $window.location.href = '?domain=' + $scope.select_domain;
                }


            }])
    .controller('ArticleController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.article = {};
                $scope.image = null;
                $scope.meta = {};
                $scope.categories = [];
                $scope.tags = [];
                $scope.domain = {};

                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('text');

                    $scope.article = $window._article;
                    $scope.domain = $window._domain;
                    $scope.meta = $window._meta;
                    $scope.image = $window._image;
                    $scope.categories = $window._categories;
                    $scope.tags = $window._tags;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.save = function () {
                    $scope.article.text = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_article_data?action=save]', {
                        article: $scope.article,
                        meta: $scope.meta,
                        tags: $scope.tags
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                var href = '[link:admin_article?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });

                }


                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\Article',
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
    .controller('ReviewListCtr',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.reviews = [];

                $scope.init = function () {
                    $scope.reviews = $window._reviews;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_review_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.reviews, function (index, review) {
                                    if (review.id == id) {
                                        $scope.reviews.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_review_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.reviews, function (index, review) {
                                    if (review.id == id) {
                                        $scope.reviews[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }


            }])
    .controller('HelpImListCtr',
        ['$scope', '$http', 'dfLoading', '$window',
            function ($scope, $http, dfLoading, $window) {
                $scope.tasks = [];
                $scope.users = [];

                $scope.init = function () {
                    $scope.tasks = $window._tasks;
                    $scope.users = $window._users;
                }


                $scope.getUsersss = function (id) {
                    var email = '';
                    if ($scope.users[id]) {
                        email = $scope.users[id].email;
                    }
                    return email;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_help_im_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.tasks, function (index, task) {
                                    if (task.task_id == id) {
                                        $scope.tasks.splice(index, 1);
                                        return false;
                                    }
                                });
                                alert(response.ok);
                                return;
                            }
                        });
                }


            }])
    .controller('HelpMessagesCtrl',
        ['$scope', '$http', 'dfLoading', 'dfNotice', '$window', '$sce',
            function ($scope, $http, dfLoading, dfNotice, $window, $sce) {
                $scope.type = [];
                $scope.status = [];
                $scope.messages = [];
                $scope.task = {};
                $scope.form = {};

                $scope.init = function () {
                    CKEDITOR.replace('text');
                    $scope.type = $window._type;
                    $scope.status = $window._status;
                    $scope.messages = $window._messages;
                    $scope.task = $window._task;
                }

                $scope.to_trusted = function (html_code) {
                    return $sce.trustAsHtml(html_code);
                }


                $scope.re_status = function (status) {
                    dfLoading.loading();
                    $http.post('[link:admin_help_im_data?action=reStatus]', {status: status, id: $scope.task.task_id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.ok) {
                                $scope.task.status = response.status;
                                $scope.task.status_name = response.status_name;
                                dfNotice.ok(response.ok)
                            }
                            if (response.error) {
                                dfNotice.error(response.error)
                            }
                        });
                };

                $scope.send = function () {
                    $scope.form.text = CKEDITOR.instances.text.getData();
                    if (!$scope.form.text) {
                        dfNotice.error('Напишите Ваш комментарий');
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_help_im_data?action=addMassage]', {
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
    .controller('ReviewController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.review = {};

                $scope.init = function () {
                    $scope.review = $window._review;
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_review_data?action=save]', {review: $scope.review})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                var href = '[link:admin_review?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });

                };

            }])
    .controller('SubscriberListCtr',
        ['$scope', '$http', 'dfLoading', 'dfNotice',
            function ($scope, $http, dfLoading, dfNotice) {
                $scope.subs = [];
                $scope.isEdit = null;
                $scope.search = {email: ""};

                $scope.init = function (subs) {
                    $scope.subs = subs;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_review_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.reviews, function (index, review) {
                                    if (review.review_id == id) {
                                        $scope.reviews.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };

                $scope.delete = function (id) {
                    dfLoading.loading();

                    $http.post('[link:admin_subscriber_data?action=delete]', {id: id})
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
                        alert('Произошла ошибка');
                    });

                }


                $scope.isNotEdit = function (item) {
                    if ($scope.isEdit == null)
                        return true;

                    if ($scope.isEdit.id == item.id)
                        return false;
                    else
                        return true;
                }

                $scope.startEdit = function (item) {
                    $scope.isEdit = item;
                }

                $scope.cancelEdit = function () {
                    $scope.isEdit = null;
                }

                $scope.saveEdit = function (item) {
                    dfLoading.loading();

                    $http.post('[link:admin_subscriber_data?action=save]', item)
                        .success(function (response) {
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.isEdit = null;
                            }
                            dfLoading.ready();
                            alert('Готово');
                        }).error(function (response) {
                        dfLoading.ready();
                        alert('Произошла ошибка');
                    });


                };

                $scope.search_form = function (search) {
                    var url = '[link:admin_subscriber?action=list]';

                    if (search) {
                        url += '?search=1&email=' + $scope.search.email;
                    }
                    window.location.href = url;

                }


            }])
    .controller('DeliveryAddController',
        ['$scope', '$http', 'dfLoading', '$window',
            function ($scope, $http, dfLoading, $window) {
                $scope.delivery = {};
                $scope.test_email = null;
                $scope.groups = {};
                $scope.currentGroup = {};

                $scope.init = function () {
                    CKEDITOR.replace('message');
                    $scope.delivery = $window._delivery;
                    $scope.groups = $window._groups;
                    if ($window._group_id) {
                        $.each($scope.groups, function (index, item) {
                            if (item.id == $window._group_id) {
                                $scope.currentGroup = item;
                                return false;
                            }
                        });
                    }
                }

                $scope.save = function () {
                    $scope.delivery.group_id = $scope.currentGroup.id || 0;
                    $scope.delivery.message = CKEDITOR.instances.message.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_delivery_data?action=save]', $scope.delivery)
                        .success(function (response) {
                            if (response.errors.length) {
                                $scope.errors = response.errors;
                            }

                            if (response.ok.length) {
                                $scope.errors = {};
                                if (!confirm("Готово, остаться на странице? ")) {
                                    var href = '[link:admin_delivery?action=list]';
                                    window.location.href = href;
                                } else {
                                    var href = '[link:admin_delivery?action=edit]?id=' + response.id;
                                    window.location.href = href;
                                }
                            }
                            dfLoading.ready();
                        });
                }

                $scope.reset = function () {
                    $http.post('[link:admin_delivery_data?action=reset]', $scope.delivery)
                        .success(function (response) {
                            if (response.error.length) {
                                alert(response.error);

                            }
                            if (response.ok.length) {
                                if (!confirm("Готово, остаться на странице? ")) {
                                    var href = '[link:admin_delivery?action=list]';
                                    window.location.href = href;
                                } else {
                                    var href = '[link:admin_delivery?action=edit]?id=' + response.id;
                                    window.location.href = href;
                                }
                            }
                            dfLoading.ready();
                        });
                }

                $scope.test = function () {
                    if ($scope.test_email == null) {
                        alert('Укажите email для отправки письма');
                        return;
                    }
                    dfLoading.loading();
                    $scope.delivery.group_id = $scope.currentGroup.id || 0;
                    $scope.delivery.message = CKEDITOR.instances.message.getData();
                    $http.post('[link:admin_delivery_data?action=test]', {
                        email: $scope.test_email,
                        delivery: $scope.delivery
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error.length) {
                                alert(response.error);
                            }
                            if (response.ok.length) {
                                alert(response.ok);
                            }
                        });
                };


            }])
    .controller('DeliverySendController',
        ['$scope', '$http', 'dfLoading', 'dfNotice',
            function ($scope, $http, dfLoading, dfNotice) {
                $scope.form = {};

                $scope.init = function () {
                    CKEDITOR.replace('message');
                    ;
                }

                $scope.send = function () {
                    if ($scope.form.email == null) {
                        dfNotice.error('Укажите email для отправки письма');
                        return;
                    }
                    dfLoading.loading();
                    $scope.form.message = CKEDITOR.instances.message.getData();
                    $http.post('[link:admin_delivery_data?action=sendMail]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                            }
                        });
                };


            }])
    .controller('GoController',
        ['$scope', '$http', 'dfLoading', '$window',
            function ($scope, $http, dfLoading, $window) {
                $scope.go = {};
                $scope.url = '[link:go?url=_w_]';
                $scope.add_form = false;
                $scope.edit_form = false;
                $scope.new_go = {redirect: "", comment: ""};
                $scope.edit_go = {url: "", comment: "", redirect: "", visit: "", go_id: ""};


                $scope.init = function () {
                    $scope.go = $window._go;
                }

                $scope.getUrl = function (hash) {
                    var url = $scope.url;
                    return url.replace("_w_", hash);
                };

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_go_data?action=add]', $scope.new_go)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                temp = angular.copy($scope.go);
                                $scope.go = new Array();
                                $scope.go.push(response.go);
                                $.each(temp, function (index, item) {
                                    $scope.go.push(item);
                                });
                            }
                            $scope.close_form_add();
                        });
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_go_data?action=edit]', $scope.edit_go)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.go, function (index, item) {
                                    if (item.go_id == $scope.edit_go.go_id) {
                                        $scope.go[index] = $scope.edit_go;
                                    }
                                });
                            }
                            $scope.close_form_edit();
                        });
                }

                $scope.reset = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_go_data?action=clear]', $scope.edit_go)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.go, function (index, item) {
                                    if (item.go_id == $scope.edit_go.go_id) {
                                        $scope.go[index] = response.go;
                                    }
                                });
                            }
                            $scope.close_form_edit();
                        });
                }

                $scope.open_form_add = function () {
                    $scope.add_form = true;
                    $scope.new_go = {url: "", comment: ""};
                    $scope.close_form_edit();
                }

                $scope.close_form_add = function () {
                    $scope.add_form = false;
                    $scope.new_go = {url: "", comment: ""};
                }

                $scope.open_form_edit = function (item) {
                    $scope.edit_form = true;
                    $scope.edit_go = angular.copy(item);
                    $scope.close_form_add();
                }

                $scope.close_form_edit = function () {
                    $scope.edit_form = false;
                    $scope.edit_go = {url: "", comment: "", redirect: "", visit: "", go_id: ""};
                }


            }])
    .controller('MailController',
        ['$scope', '$http', '$window', 'dfLoading',
            function ($scope, $http, $window, dfLoading) {
                $scope.form = true;
                $scope.groups = {};
                $scope.currentGroup = null;
                $scope.countMail = 0;
                $scope.countEnd = 0;
                $scope.progress = 0;
                $scope.limit = 5;
                $scope.mail = {subject: "", text: ""};
                $scope.is_send = false;


                $scope.init = function () {
                    CKEDITOR.replace('text');
                    $scope.groups = $window._groups;
                    if ($window._group_id) {
                        $.each($scope.groups, function (index, item) {
                            if (item.id == $window._group_id) {
                                $scope.currentGroup = item;
                                $scope.recalculate();
                                return false;
                            }
                        });
                    }
                }

                $scope.recalculate = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_delivery_data?action=countMailGroup]', $scope.currentGroup)
                        .success(function (response) {
                            $scope.is_send = true;
                            $scope.countMail = response.count;
                            dfLoading.ready();
                        });
                };


                $scope.send = function () {
                    if ($scope.currentGroup == null || parseInt($scope.countMail) == 0) {
                        alert('Вы не выбрали группу подписчиков');
                        return;
                    }

                    $scope.mail.text = CKEDITOR.instances.text.getData();

                    if ($scope.mail.text.length == 0) {
                        alert("Укажите текст сообщения");
                    }

                    if ($scope.mail.subject.length == 0) {
                        alert("Укажите заголовок сообщения");
                    }
                    $scope.countEnd = parseInt($scope.countMail);
                    $scope.form = false;
                    $scope.mailing(0);
                }

                $scope.mailing = function (offset) {

                    $http.post('[link:admin_delivery_data?action=sendMails]',
                        {
                            limit: $scope.limit,
                            groupId: $scope.currentGroup.id,
                            offset: offset,
                            mail: $scope.mail
                        })
                        .success(function (response) {
                            $scope.countEnd = $scope.countEnd - $scope.limit;
                            $scope.setProgress($scope.countEnd, $scope.countMail)

                            if ($scope.countEnd > 0) {
                                $scope.mailing($scope.limit + offset);
                            }
                            else {
                                $scope.form = true;
                                $scope.progress = 0;
                            }
                        });
                }

                $scope.setProgress = function (end, value) {
                    var pres = value - end;
                    $scope.progress = parseInt((pres * 100) / value);
                }


            }])
    .controller('PageListController',
        ['$scope', '$http', '$window', 'dfLoading', 'dfImage', 'dfNotice',
            function ($scope, $http, $window, dfLoading, dfImage, dfNotice) {
                $scope.pages = {};
                $scope.domain = {};
                $scope.images = [];
                $scope.select_domain = 'www';
                $scope.show_pages = [];
                $scope.select_pages = [];

                $scope.init = function () {
                    $scope.pages = $window._pages;
                    $scope.domain = $window._domain;
                    $scope.images = $window._images;
                }

                $scope.select = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_page_data?action=pages]', {site: $scope.select_domain})
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.pages = response.pages;
                            $scope.images = response.images;
                        });
                }

                $scope.show_page = function (page) {
                    if (page.pid == 0) {
                        return true;
                    }
                    if ($scope.show_pages[page.pid]) {
                        return true;
                    }
                    return false;
                }

                $scope.show_page_child = function (page) {
                    if ($scope.show_pages[page.id]) {
                        delete($scope.show_pages[page.id]);
                        delete($scope.select_pages[page.id]);
                    }
                    else {
                        $scope.show_pages[page.id] = true;
                        if ($scope.has_child(page)) {
                            $scope.select_pages[page.id] = true;
                        }
                    }
                }

                $scope.is_selected = function (page) {
                    if ($scope.select_pages[page.id]) {
                        return true;
                    }
                    return false;
                }

                $scope.has_child = function (page) {
                    if (page.children != 0) {
                        return true;
                    }
                    return false;
                }

                $scope.getPages = function (pid) {
                    var pages = [];
                    $.each($scope.pages, function (index, page) {
                        if (page.pid == pid) {
                            pages.push(page);
                        }
                    });
                    return pages;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_page?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.pages, function (index, page) {
                                    if (page.id == id) {
                                        $scope.pages.splice(index, 1);
                                        return false;
                                    }
                                });
                                alert(response.ok);
                                return;
                            }
                        });
                }

                $scope.add = function (id) {
                    var href = '[link:admin_page?action=add]?id=' + id;
                    window.location.href = href;
                }

                $scope.edit = function (id) {
                    var href = '[link:admin_page?action=edit]?id=' + id;
                    window.location.href = href;
                }

                $scope.up = function (page) {
                    $scope.ajaxChangePos(page.id, page.pos, 'up');
                }

                $scope.down = function (page) {
                    $scope.ajaxChangePos(page.id, page.pos, 'down');
                }

                $scope.change_pos = function (page) {
                    $scope.ajaxChangePos(page.id, page.pos, 'edit');
                }

                $scope.ajaxChangePos = function (id, pos, type) {
                    dfLoading.loading();
                    $http.post('[link:admin_page_data?action=changePos]', {id: id, pos: pos, type: type})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.pages = response.pages;
                                return;
                            }
                        });
                }

                $scope.main = function (id, main) {
                    dfLoading.loading();
                    $http.post('[link:admin_page_data?action=main]', {id: id, main: main})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.pages = response.pages;
                                return;
                            }
                        });
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_page_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.pages = response.pages;
                                return;
                            }
                        });
                }


                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\Page',
                            function (data, status, headers, config) {
                                dfLoading.ready();
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

                $scope.getImageSrc = function (id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            src = image.preview;
                        }
                    });
                    return src;
                }

            }])
    .controller('PageController',
        ['$scope', '$http', '$window', 'dfLoading', 'dfNotice', 'dfImage',
            function ($scope, $http, $window, dfLoading, dfNotice, dfImage) {
                $scope.image = [];
                $scope.options = {};
                $scope.templates = {};
                $scope.errors = {};
                $scope.page = {};
                $scope.dir = null;
                $scope.tpl = null;
                $scope.mobile = null;
                $scope.domains = [];
                $scope.select_domain = 'www';
                $scope.select_domain_form = true;

                $scope.init = function () {
                    $scope.templates = $window._templates;
                    $scope.options = $window._options;
                    $scope.page = $window._page;
                    $scope.page.pid = $window._pid;
                    $scope.image = $window._image;
                    $scope.domains = $window._domains;
                    if ($window._site != null) {
                        $scope.select_domain_form = false;
                        $scope.select_domain = $window._site;
                    } else {
                        $scope.select_domain_form = false;
                        $scope.select_domain = $scope.page.site;
                    }

                    $scope.dir = $scope.page.template_dir || $window._dir
                    $scope.tpl = $scope.page.template_page || $window._tpl
                    $scope.mobile = $scope.page.mobile || $window._mobile;
                    CKEDITOR.replace('text');
                }

                $scope.showHost = function () {
                    var host = '';
                    $.each($scope.domains, function (index, item) {
                        if (item.name == $scope.select_domain) {
                            host = item.host;
                        }
                    });
                    return host;
                }

                $scope.save = function () {
                    $scope.page.template_page = $scope.tpl;
                    $scope.page.template_dir = $scope.dir;
                    $scope.page.site = $scope.select_domain;
                    $scope.page.text = CKEDITOR.instances.text.getData();
                    $http.post('[link:admin_page_data?action=save]', {
                        page: $scope.page,
                        options: $scope.options
                    })
                        .success(function (response) {
                            if (response.errors.length) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok.length) {
                                $scope.errors = {};
                                if (!confirm("Готово, остаться на странице? ")) {
                                    var href = '[link:admin_page?action=list]';
                                    window.location.href = href;
                                } else {
                                    var href = '[link:admin_page?action=edit]?id=' + response.page.id;
                                    window.location.href = href;
                                }
                            }
                        });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\Page',
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
    .controller('RootController',
        ['$scope', '$http',
            function ($scope, $http) {
                $scope.root = {};

                $scope.init = function (root) {
                    $scope.root = root;
                }

                $scope.save = function () {

                    if ($scope.root.login == null) {
                        alert('Укажите имя');
                        return;
                    }
                    if ($scope.root.admin_id == null && $scope.root.newPassword == null) {
                        alert('Укажите пароль');
                        return;
                    }

                    if ($scope.root.newPassword != null && $scope.root.newPassword != $scope.root.newPasswordVerify) {
                        alert('Пароли не совпадают');
                        return;
                    }

                    $http.post('[link:admin_root_data?action=save]', $scope.root)
                        .success(function (response) {
                            if (response.errors.length) {
                                $scope.errors = response.errors;
                                alert('Исправте ошибки');
                            }

                            if (response.ok.length) {
                                $scope.errors = {};
                                if (!confirm("Готово, остаться на странице? ")) {
                                    var href = '[link:admin_root?action=list]';
                                    window.location.href = href;
                                } else {
                                    var href = '[link:admin_root?action=edit]?id=' + response.root.admin_id;
                                    window.location.href = href;
                                }
                            }
                        });
                }

            }])
    .controller('UserListCtrl',
        ['$scope', '$http', 'dfLoading', '$window',
            function ($scope, $http, dfLoading, $window) {
                $scope.users = [];
                $scope.attr_name = [];
                $scope.user_attrs = [];
                $scope.form = {};
                $scope.form_search = 0;
                $scope.balance = [];
                $scope.balance_isset = false;

                $scope.search = function () {
                    var get = '?';
                    var i = false;
                    $.each($scope.form, function (name, value) {
                        if (value) {
                            if (i) {
                                get += '&' + name + '=' + value;
                            } else {
                                i = true;
                                get += name + '=' + value;
                            }
                        }
                    });

                    var url = '[link:admin_user]' + get;
                    window.location.href = url;
                };


                $scope.cancel = function () {
                    var url = '[link:admin_user]';
                    window.location.href = url;

                }

                $scope.init = function () {
                    $scope.users = $window._users;
                    $scope.attr_name = $window._attr_name;
                    $scope.user_attrs = $window._user_attrs;
                    $scope.balance = $window._balance;
                    $scope.balance_isset = $window._balance_isset;

                    setTimeout(function () {
                        $scope.$apply(function () {
                            $scope.form = $window._form;
                        });
                    }, 100);
                }

                $scope.getBalance = function (user_id) {
                    if ($scope.balance[user_id])
                        return $scope.balance[user_id].value;

                }

                $scope.banned = function (id) {
                    if (!confirm('Вы действительно хотите забанить пользователя?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_user_data?action=banned]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            $.each($scope.users, function (index, user) {
                                if (user.user_id == id) {
                                    $scope.users[index].active = 0;
                                    return false;
                                }
                            });
                        });
                }

                $scope.login = function (id) {
                    dfLoading.loading();
                    $http.post('[link:admin_user_data?action=auth]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            alert('Вы авторизованы под пользоватлем');
                        });
                }

                $scope.unbanned = function (id) {
                    if (!confirm('Вы действительно хотите разбанить пользователя?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_user_data?action=unbanned]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            $.each($scope.users, function (index, user) {
                                if (user.user_id == id) {
                                    $scope.users[index].active = 1;
                                    return false;
                                }
                            });
                        });
                }

                $scope.changePassword = function (id) {
                    if (!confirm('Вы действительно хотите выслать новый пароль пользователю?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_user_data?action=remind]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            alert('Пароль изменен и выслан');

                        });
                }


            }])
    .controller('BalanceCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {

                $scope.user = {};
                $scope.balance = {};
                $scope.cashflow = {};
                $scope.form = {type: 1};

                $scope.init = function () {
                    $scope.user = $window._user;
                    $scope.balance = $window._balance;
                    $scope.cashflow = $window._cashflow;
                };

                $scope.send = function () {
                    if ($scope.form.value == null) {
                        dfNotice.error('Укажити сумму операции');
                        return;
                    }

                    if ($scope.form.reason == null) {
                        dfNotice.error('Укажити основания длч операции');
                        return;
                    }

                    dfLoading.loading();
                    $scope.form.user_id = $scope.user.user_id;
                    $scope.form.balance_id = $scope.balance.balance_id;
                    $http.post('[link:admin_user_data?action=balance]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                window.location.reload();
                            }

                        });

                }
            }])
    .controller('UserAddController',
        ['$scope', '$http', '$window', 'dfLoading', 'dfNotice',
            function ($scope, $http, $window, dfLoading, dfNotice) {
                $scope.user = {};

                $scope.init = function () {
                    $scope.user = $window._user;
                    $scope.groups = $window._groups;
                    $scope.attr_name = {};
                    $scope.attr_value = {};
                    $.each($window._attr_name, function (index, item) {
                        if (!$scope.attr_name[item.group_id])
                            $scope.attr_name[item.group_id] = {};
                        $scope.attr_name[item.group_id][index] = item;
                        if (!$scope.attr_value[item.group_id])
                            $scope.attr_value[item.group_id] = {};
                        $scope.attr_value[item.group_id][item.id] = $window._attr_value[index] ?
                            $window._attr_value[index] : {value: '', attr_id: item.id, group_id: item.group_id, pos: 0};
                    });
                };

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_user_data?action=save]', {user: $scope.user, attr: $scope.attr_value})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.error(response.errors)
                                $scope.errors = response.errors;
                            }

                            if (response.ok) {
                                $scope.errors = {};
                                if (!confirm("Готово, остаться на странице? ")) {
                                    var href = '[link:admin_user?action=list]';
                                    window.location.href = href;
                                } else {
                                    var href = '[link:admin_user?action=edit]?id=' + response.id;
                                    window.location.href = href;
                                }
                            }
                        });
                }
            }])
    .controller('SubscriptionEditController',
        ['$scope', '$http', '$window', 'dfLoading',
            function ($scope, $http, $window, dfLoading) {
                $scope.sub = {};
                $scope.config = {};
                $scope.errors = {};

                $scope.init = function () {
                    $scope.sub = $window._sub || {type: 'sub'};
                    $scope.config = $window._config || {};
                }


                $scope.save = function () {
                    dfLoading.loading();
                    if ($scope.sub.group_id) {
                        $http.post('[link:admin_subscription_data?action=edit]', {
                            sub: $scope.sub,
                            config: $scope.config
                        })
                            .success(function (response) {
                                dfLoading.ready();
                                if (response.errors) {
                                    $scope.errors = response.errors;
                                    alert('Исправьте ошибки');
                                    return;
                                }

                                if (response.ok) {
                                    $scope.errors = {};
                                    alert('Готово');
                                }
                            });
                    }
                    else {
                        $http.post('[link:admin_subscription_data?action=add]', {
                            sub: $scope.sub,
                            config: $scope.config
                        })
                            .success(function (response) {
                                dfLoading.ready();
                                if (response.errors) {
                                    $scope.errors = response.errors;
                                    alert('Исправьте ошибки');
                                    return;
                                }

                                if (response.ok) {
                                    $scope.errors = {};
                                    var href = '[link:admin_subscription?action=list]';
                                    window.location.href = href;
                                }
                            });
                    }
                }

            }])
    .controller('SubscriptionListCtr',
        ['$scope', '$http', 'dfLoading', '$window',
            function ($scope, $http, dfLoading, $window) {
                $scope.subs = {};
                $scope.url = '[link:form_show?url=_id_]';

                $scope.init = function () {
                    $scope.subs = $window._subs;
                }

                $scope.getUrl = function (id) {
                    var url = $scope.url;
                    return url.replace("_id_", id);
                };

            }])
    .controller('DocumentListController',
        ['$scope', '$http', '$window', 'dfLoading', '$upload',
            function ($scope, $http, $window, dfLoading, $upload) {
                $scope.files = [];
                $scope.get = [];
                $scope.categories = [];

                $scope.init = function () {
                    $scope.get = $window._get;
                    $scope.files = $window._files;
                    $scope.categories = $window._categories;
                };

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.changeCid = function (item) {
                    $http.post('[link:admin_doc_data?action=save]', item)
                        .success(function (response) {

                        });
                };

                $scope.delete = function (item) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_doc_data?action=delete]', item)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.files, function (index, file) {
                                    if (file.file_id == item.file_id) {
                                        $scope.files.splice(index, 1);
                                        return false;
                                    }
                                });
                            }

                        }).error(function (response) {
                        dfLoading.ready();
                        alert('Произошла ошибка');
                    });
                };


                $scope.onFileSelect = function ($files, get) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        $scope.upload = $upload.upload({
                            url: '[link:admin_doc_data?action=upload]',
                            file: file,
                            data: get
                        }).progress(function (evt) {
                            console.log(evt);
                            console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
                        }).success(function (data, status, headers, config) {
                            i -= 1;
                            dfLoading.ready('image' + i);
                            if (data.error) {
                                alert(data.error);
                            }
                            else {

                                temp = angular.copy($scope.files);
                                $scope.files = [];
                                $scope.files.push(data);
                                $.each(temp, function (index, item) {
                                    $scope.files.push(item);
                                });

                            }
                        });
                    }
                };

            }])
    .controller('DocumentController',
        ['$scope', '$http', 'dfLoading', '$window', '$upload', 'dfNotice',
            function ($scope, $http, dfLoading, $window, $upload, dfNotice) {
                $scope.file = {};
                $scope.categories = [];
                $scope.imageExtensions = ['jpeg', 'jpg', 'png', 'bmp', 'gif']
                $scope.init = function () {
                    $scope.file = $window._file;
                    $scope.categories = $window._categories;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_doc_data?action=save]', $scope.file)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                var href = '[link:admin_doc?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });
                }


                $scope.onFileSelect = function ($files, file) {
                    id = file.file_id;
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        $scope.upload = $upload.upload({
                            url: '[link:admin_doc_data?action=upload]?file_id=' + id,
                            data: {file: file},
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
                                $scope.file = data;
                            }
                        });
                    }
                };

            }])
    .controller('FileIndexListController',
        ['$scope', '$http', '$window', 'dfLoading', '$upload',
            function ($scope, $http, $window, dfLoading, $upload) {
                $scope.files = [];


                $scope.init = function () {
                    $scope.files = $window._files;
                };


                $scope.delete = function (item) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }

                    dfLoading.loading();
                    $http.post('[link:admin_fileIndex_data?action=delete]', item)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.files, function (index, file) {
                                    if (file.file_id == item.file_id) {
                                        $scope.files.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                            alert('Готово');
                        }).error(function (response) {
                        dfLoading.ready();
                        alert('Произошла ошибка');
                    });
                };


                $scope.onFileSelect = function ($files) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        $scope.upload = $upload.upload({
                            url: '[link:admin_fileIndex_data?action=upload]',
                            file: file,
                        }).progress(function (evt) {
                            console.log(evt);
                            console.log('percent: ' + parseInt(100.0 * evt.loaded / evt.total));
                        }).success(function (data, status, headers, config) {
                            dfLoading.ready();
                            if (data.error) {
                                alert(data.error);
                            }
                            else {

                                temp = angular.copy($scope.files);
                                $scope.files = new Array();
                                $scope.files.push(data);
                                $.each(temp, function (index, item) {
                                    $scope.files.push(item);
                                });

                            }
                        });
                    }
                };

            }])
    .controller('GalleriesListController',
        ['$scope', '$http', '$window', 'dfLoading',
            function ($scope, $http, $window, dfLoading) {
                $scope.galleries = [];
                $scope.errors = [];
                $scope.form_edit = 0;
                $scope.form = {};
                $scope.domain = {};
                $scope.select_domain = 'www';

                $scope.init = function () {
                    $scope.galleries = $window._galleries;
                    $scope.categories = $window._categories;
                    $scope.domain = $window._domain;
                    $scope.select_domain = $window._select_domain;
                };

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.select = function () {
                    $window.location.href = '?domain=' + $scope.select_domain;
                }

                $scope.getNameCat = function (cid) {
                    var name = '';
                    $.each($scope.categories, function (index, item) {
                        if (item.value == cid) {
                            name = item.name;
                        }
                    });

                    return name;
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_gallery_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.galleries, function (index, gallery) {
                                    if (gallery.gallery_id == id) {
                                        $scope.galleries[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }

                $scope.delete = function (item) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_gallery_data?action=delete]', item)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                $scope.errors = response.errors;
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.galleries, function (index, gallery) {
                                    if (gallery.gallery_id == item.gallery_id) {
                                        $scope.galleries.splice(index, 1);
                                        return false;
                                    }
                                });
                            }
                            alert('Готово');
                        }).error(function (response) {
                        dfLoading.ready();
                        alert('Произошла ошибка');
                    });
                };

                $scope.truncate = function (str, maxlength) {
                    if (str.length > maxlength) {
                        return str.slice(0, maxlength - 1) + '\u2026';
                    }
                    return str;
                }

                $scope.change = function (gallery) {
                    dfLoading.loading();
                    $http.post('[link:admin_gallery_data?action=save]', gallery)
                        .success(function (response) {
                            dfLoading.ready();
                        });
                }


                $scope.ajaxChangePos = function (id, pos, type) {
                    dfLoading.loading();
                    $http.post('[link:admin_gallery_data?action=changePos]', {id: id, pos: pos, type: type})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.galleries = response.galleries;
                                return;
                            }
                        });
                }

            }])
    .controller('GalleryImagesListController',
        ['$scope', '$http', '$window', 'dfLoading', 'dfImage', 'dfNotice',
            function ($scope, $http, $window, dfLoading, dfImage, dfNotice) {
                $scope.images = [];
                $scope.gallery = {};
                $scope.ckeditor = [];
                $scope.categories = [];
                $scope.domain = [];
                $scope.select_domain = 'www';

                $scope.init = function () {
                    $scope.images = $window._images;
                    $scope.gallery = $window._gallery;
                    $scope.categories = $window._categories;
                    $scope.domain = $window._domain;
                    $scope.select_domain = $window._select_domain;
                };

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.save = function () {
                    $scope.gallery.site = $scope.select_domain;
                    dfLoading.loading();
                    $http.post('[link:admin_gallery_data?action=save]', $scope.gallery)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.gallery = response.gallery;
                                return;
                            }
                        });
                }

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
                }

                $scope.delete = function (item) {
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


                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading('image' + i);
                        dfImage.add(file, id,
                            '\\CMS\\Core\\Entity\\Gallery',
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

                $scope.main = function (image, status) {
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
                };

                $scope.change = function (image) {
                    dfLoading.loading();
                    dfImage.save(image,
                        function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {

                                return;
                            }
                        });
                }


            }])
    .controller('CallbackListCtr',
        ['$scope', '$http', '$window', 'dfLoading',
            function ($scope, $http, $window, dfLoading) {
                $scope.callback = [];
                $scope.users = [];
                $scope.init = function () {
                    $scope.callback = $window._callback;
                    $scope.users = $window._users;
                }

                $scope.treat = function (callback_id) {
                    dfLoading.loading();
                    $http.post('[link:admin_callback_data?action=treat]', {id: callback_id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.callback, function (index, callback) {
                                    if (callback.callback_id == response.callback.callback_id) {
                                        $scope.callback[index] = response.callback;
                                        return true;
                                    }
                                });
                            }
                        });
                }

                $scope.delete = function (callback_id) {
                    $http.post('[link:admin_callback_data?action=delete]', {id: callback_id});
                    $.each($scope.callback, function (index, callback) {
                        if (callback.callback_id == callback_id) {
                            $scope.callback.splice(index, 1);
                            return true;
                        }
                    });

                }

                $scope.getLogin = function (user_id) {
                    var login = '';
                    $.each($scope.users, function (index, user) {
                        if (user.admin_id == user_id) {
                            login = user.login;
                            return true;
                        }
                    });
                    return login;
                }
            }])
    .controller('AttrUserCtrl',
        ['$scope', '$window', '$http', 'dfLoading', 'dfNotice',
            function ($scope, $window, $http, dfLoading, dfNotice) {
                $scope.group = [];
                $scope.attributes = [];
                $scope.init = function () {
                    $scope.group = $window._group;
                    $.each($window._attributes, function (index, attr) {
                        $scope.add(attr);
                    });
                }

                $scope.sort_attr = function () {
                    $scope.attributes.sort(function (i, ii) {
                        if (+i.pos > +ii.pos) return 1;
                        else if (+i.pos < +ii.pos) return -1;
                        else return 0;
                    });
                };

                $scope.add = function (attr) {
                    attr = attr || {name: '', pos: 0};
                    var inc = $scope.attributes.length;
                    attr.inc = inc;
                    attr.show = 1;
                    $scope.attributes.push(attr);
                    $scope.sort_attr();
                }

                $scope.delete = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.attributes[inc].show = 0;
                    if ($scope.attributes[inc].id) {
                        $http.post('[link:admin_attr_data?action=deleteAttr]', {
                            id: $scope.attributes[inc].id
                        });
                    }
                }

                $scope.save = function () {
                    dfLoading.loading();

                    var attrs = [];
                    $.each($scope.attributes, function (inc, attr) {
                        if ($scope.attributes[inc].show == 1) {
                            attrs.push(attr);
                        }
                    });

                    $http.post('[link:admin_attr_data?action=save]', {attr: attrs, group: $scope.group})
                        .success(function (response) {
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                            }

                            if (response.ok) {
                                $scope.attributes = [];
                                $scope.group = response.group;
                                $.each(response.attr, function (index, attr) {
                                    $scope.add(attr);
                                });
                                dfNotice.ok(response.ok);
                            }
                            dfLoading.ready();
                        });
                };
            }])
    .controller('GroupUserCtrl',
        ['$scope', '$window', 'dfLoading', 'dfNotice', '$http',
            function ($scope, $window, dfLoading, dfNotice, $http) {

                $scope.init = function () {
                    $scope.groups = $window._groups;
                }

                $scope.delete = function (id) {
                    dfLoading.loading();
                    $http.post('[link:admin_attr_data?action=delete]?id=' + id, {})
                        .success(function (response) {
                            if (response.error)
                                dfNotice.error(response.error);
                            else {
                                $scope.attributes = response.attr;
                                dfNotice.ok(response.ok);
                                for (var i in $scope.groups)
                                    if ($scope.groups[i].group_id == id) {
                                        $scope.groups.splice(i, 1);
                                        break;
                                    }
                            }
                            dfLoading.ready();
                        });
                };

            }])
    .controller('RolesCtrl',
        ['$scope', '$http', 'dfLoading', '$window',
            function ($scope, $http, dfLoading, $window) {
                $scope.roles = [];
                $scope.type = null;

                $scope.init = function () {
                    $scope.roles = $window._roles;
                    $scope.type = $window._type;
                }

                $scope.getRoles = function (pid) {
                    var roles = [];
                    $.each($scope.roles, function (index, role) {
                        if (role.pid == pid) {
                            roles.push(role);
                        }
                    });
                    return roles;
                }

            }])
    .controller('OrmRoleCtrl',
        ['$scope', '$http', 'dfLoading', '$window',
            function ($scope, $http, dfLoading, $window) {
                $scope.orms = [];
                $scope.acl = [];
                $scope.type = null;
                $scope.code = null;
                $scope.list = [];

                $scope.init = function () {
                    $scope.orms = $window._orms;
                    $scope.acl = $window._acl;
                    $scope.type = $window._type;
                    $scope.code = $window._code;
                    $.each($scope.orms, function (index, item) {
                        $scope.addOrm(item);
                    });
                }

                $scope.change = function (orm, action) {
                    $http.post('[link:admin_acl_data?action=orm]', {
                        orm: orm,
                        action: action,
                        type: $scope.type,
                        code: $scope.code
                    });
                }

                $scope.addOrm = function (orm) {
                    orm.action = {create: -1, update: -1, delete: -1, read: -1};
                    $.each($scope.acl, function (index, item) {
                        if (orm.table_name == item.resource) {
                            orm.action[item.privilege] = item.status;
                        }
                    });
                    $scope.list.push(orm);
                }

            }])
    .controller('BannersListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.banners = [];

                $scope.init = function () {
                    $scope.banners = $window._banners;
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_banner_data?action=switch]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.banners, function (index, banner) {
                                    if (banner.banner_id == id) {
                                        $scope.banners[index].status = status;
                                        return;
                                    }
                                });
                            }

                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_banner_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.banners, function (index, banner) {
                                    if (banner.banner_id == id) {
                                        $scope.banners.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };

                $scope.changePos = function (banner) {
                    dfLoading.loading();
                    $http.post('[link:admin_banner_data?action=changePos]', banner)
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
    .controller('BannersController',
        ['$scope', '$http', 'dfLoading', '$window', '$upload', 'dfNotice',
            function ($scope, $http, dfLoading, $window, $upload, dfNotice) {
                $scope.banner = {};
                $scope.types = [];
                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('text');


                    $scope.banner = $window._banner;
                    $scope.types = $window._types;
                }

                $scope.save = function () {
                    $scope.banner.html = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_banner_data?action=save]', $scope.banner)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                var href = '[link:admin_banner?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });
                }

                $scope.getWidth = function () {
                    if ($scope.banner.width) {
                        return $scope.banner.width
                    } else {
                        return 100;
                    }
                }

                $scope.getHeight = function () {
                    if ($scope.banner.height) {
                        return $scope.banner.height
                    } else {
                        return 100;
                    }
                }


                $scope.onFileSelect = function ($files, banner) {

                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        $scope.upload = $upload.upload({
                            url: '[link:admin_banner_data?action=upload]',
                            data: {banner: banner},
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
                                if ($scope.banner.type_id != data.type_id) {
                                    window.location.reload();
                                }
                                $scope.banner.width = data.width;
                                $scope.banner.height = data.height;
                                $scope.banner.path = data.path;
                            }
                        });
                    }
                };

            }])
    .controller('AddHelpCtrl',
        ['$scope', '$http', 'dfLoading', '$window', '$upload', 'dfNotice',
            function ($scope, $http, dfLoading, $window, $upload, dfNotice) {
                $scope.type = [];
                $scope.status = [];
                $scope.task = {};
                $scope.form = {};

                $scope.init = function () {
                    CKEDITOR.replace('text');
                    $scope.type = $window._type;
                    $scope.status = $window._status;
                    $scope.task = $window._task;
                }

                $scope.save = function () {
                    $scope.task.text = CKEDITOR.instances.text.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_help_im_data?action=addTask]', {task: $scope.task})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                var href = '[link:admin_help_im?action=edit]?id=' + response.task_id;
                                window.location.href = href;
                            }
                        });
                }

                $scope.re_type = function (id) {
                    $.each($scope.type, function (index, item) {
                        if (item.id == id) {
                            $scope.task.type_id = item['id'];
                            $scope.task.type_name = item['name'];
                            return false;
                        }
                    });
                }

                $scope.re_status = function (id) {
                    $.each($scope.status, function (index, item) {
                        if (item.id == id) {
                            $scope.task.status = item['id'];
                            $scope.task.status_name = item['name'];
                            return false;
                        }
                    });
                }
            }])
    .controller('CategoriesListCMSCtrl',
        ['$scope', '$http', 'dfLoading', '$window', '$sce', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, $sce, dfImage, dfNotice) {
                $scope.categories = [];
                $scope.images = [];
                $scope.types = [];
                $scope.config_type = [];
                $scope.select_types = 1;
                $scope.query = '';
                $scope.select_categories = [];
                $scope.show_categories = [];

                $scope.init = function () {
                    $scope.select_types = $window._type_id;
                    $scope.config_type = $window._config_type;
                    $scope.categories = $window._categories;
                    $scope.types = $window._types;
                    $scope.images = $window._images;
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
                    if ($scope.getCategories(category.cid).length == 0) {
                        return false;
                    }
                    return true;
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
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == cid) {
                            src = image.preview;
                        }
                    });
                    return src;
                }

                $scope.getLinkAddObject = function (category) {
                    var html_code, url, name;
                    $.each($scope.config_type, function (index, config_type) {
                        if (config_type.id == $scope.select_types) {
                            param = config_type.param || 'cid';
                            url = config_type.add + '?' + param + '=' + category.cid;
                            name = config_type.addName;
                            return true;
                        }
                    });
                    html_code = '<a href="' + url + '"><i class="glyphicon glyphicon-shopping-cart"></i> Добавить ' + name + '</a>';
                    return $sce.trustAsHtml(html_code);
                }

                $scope.getLinkObject = function (category) {
                    var html_code, url, name;
                    $.each($scope.config_type, function (index, config_type) {
                        if (config_type.id == $scope.select_types) {
                            param = config_type.param || 'cid';
                            url = config_type.add + '?' + param + '=' + category.cid;
                            name = config_type.name;
                            return true;
                        }
                    });
                    html_code = '[<a href="' + url + '">' + name + '</a> :' + category.object + 'шт.]';
                    return $sce.trustAsHtml(html_code);
                }

                $scope.getBtnUpdateCount = function () {
                    var html_code, url = null;
                    $.each($scope.config_type, function (index, config_type) {
                        if (config_type.id == $scope.select_types) {
                            url = config_type.update;
                            return true;
                        }
                    });
                    if (url == null) {
                        return '';
                    }
                    html_code = '<a style="float:right;" title="Синхронизировать каталог" class="btn btn-danger btn-xs" href="' + url + '"><i class="glyphicon glyphicon-refresh"></i></a>';
                    return $sce.trustAsHtml(html_code);
                }


                $scope.select = function (typeId) {
                    $scope.select_types = typeId;
                    dfLoading.loading();
                    $scope.categories = [];
                    $http.post('[link:admin_cms_category_data?action=categories]', {type_id: typeId})
                        .success(function (response) {
                            dfLoading.ready();
                            $scope.categories = response.categories;
                            $scope.images = response.images;
                        });
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
                    $http.post('[link:admin_cms_category_data?action=changePos]', {id: id, pos: pos, type: type})
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
                    $http.post('[link:admin_cms_category_data?action=delete]', {id: id})
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
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Catalog\\Entity\\Category',
                            function (data, status, headers, config) {
                                dfLoading.ready();
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
                        var url = '[link:admin_cms_category?action=editIds]';
                        url += '?ids=' + ids + '&type_id=' + $scope.select_types;
                        $window.location.href = url;
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_cms_category_data?action=selectEdit]', {
                        action: action,
                        ids: ids
                    })
                        .success(function (response) {
                            var url = '[link:admin_cms_category?action=list]?type_id=' + $scope.select_types;
                            $window.location.href = url;
                        });

                }

            }])
    .controller('CategoryCMSController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.category = {};
                $scope.image = null;
                $scope.meta = {};
                $scope.ckeditor = null;
                $scope.select = [];

                $scope.init = function () {
                    $scope.select = $window._select;
                    $scope.ckeditor = CKEDITOR.replace('description');

                    $scope.category = $window._category;
                    $scope.meta = $window._meta;
                    $scope.category.pid = $window._pid;
                    $scope.category.type_id = $window._type_id;
                    $scope.image = $window._image;
                    $scope.getQuery();
                }


                $scope.getQuery = function () {
                    if ($scope.category.cid) {
                        $scope.query = '?id=' + $scope.category.cid;
                    } else {
                        $scope.query = '?type=' + $scope.category.type_id;
                    }
                }

                $scope.save = function () {
                    $scope.category.description = $scope.ckeditor.getData();

                    dfLoading.loading();
                    if ($scope.category.cid)
                        $http.post('[link:admin_cms_category_data?action=edit]', {
                            category: $scope.category,
                            meta: $scope.meta
                        })
                            .success(function (response) {
                                dfLoading.ready();
                                if (response.errors) {
                                    dfNotice.errors(response.errors);
                                }

                                if (response.ok) {
                                    dfNotice.ok(response.ok);
                                    $scope.category = response.category;
                                    $scope.update();
                                }
                            });
                    else
                        $http.post('[link:admin_cms_category_data?action=add]', {
                            category: $scope.category,
                            meta: $scope.meta
                        })
                            .success(function (response) {
                                dfLoading.ready();

                                if (response.errors) {
                                    dfNotice.errors(response.errors);
                                }

                                if (response.ok) {
                                    var href = '[link:admin_cms_category?action=edit]?id=' + response.id;
                                    window.location.href = href;
                                }
                            });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Catalog\\Entity\\Category',
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
    .controller('CategoryCMSIdsController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.category = {};
                $scope.meta = {};
                $scope.ckeditor = null;
                $scope.select = [];
                $scope.ids = [];

                $scope.init = function () {
                    $scope.select = $window._select;
                    $scope.ids = $window._ids;
                    $scope.ckeditor = CKEDITOR.replace('description');
                };

                $scope.save = function () {
                    $scope.category.description = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_cms_category_data?action=saveIds]', {
                        category: $scope.category,
                        meta: $scope.meta,
                        ids: $scope.ids
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                            }
                        });

                };


            }])
    .controller('PollListController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.polls = [];
                $scope.get = [];
                $scope.init = function () {
                    $scope.polls = $window._polls;
                    $scope.get = $window._get;
                }

                $scope.delete = function (poll_id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[link:admin_poll_data?action=delete]', {id: poll_id});
                    $.each($scope.polls, function (index, poll) {
                        if (poll.poll_id == poll_id) {
                            $scope.polls.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_poll_data?action=status]', {id: id, status: status, get: $scope.get})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.polls = response.polls;
                            }
                        });
                }

            }])
    .controller('PollEditController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.poll = {};
                $scope.items = [];

                $scope.init = function () {
                    $scope.poll = $window._poll;
                    $.each($window._items, function (index, value) {
                        $scope.addItem(value);
                    });
                }

                $scope.addItem = function (item) {
                    item = item || {};
                    var inc = $scope.items.length;
                    item.inc = inc;
                    item.delete = 0;
                    $scope.items.push(item);
                }

                $scope.deleteItem = function (inc) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $scope.items[inc].delete = 1;
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_poll_data?action=save]', {
                        poll: $scope.poll,
                        items: $scope.items
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.poll = response.poll;
                                $scope.items = [];
                                $.each(response.items, function (index, value) {
                                    $scope.addItem(value);
                                });
                            }
                        });
                }

            }])
    .controller('SliderListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.sliders = [];
                $scope.images = [];
                $scope.select_code = null;

                $scope.init = function () {
                    $scope.sliders = $window._sliders;
                    $scope.images = $window._images;
                }

                $scope.select = function (code) {
                    if (code) {
                        $scope.tmp = $scope.sliders;
                        $scope.select_code = code;
                        $scope.sliders = [];
                        $.each($scope.tmp, function (index, slider) {
                            if (slider.code == code) {
                                $scope.sliders.push(slider);
                            }
                        });
                    } else {
                        $scope.sliders = $scope.tmp;
                        $scope.select_code = null;
                    }
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_slider_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.sliders, function (index, slider) {
                                    if (slider.id == id) {
                                        $scope.sliders[index].status = status;
                                        return false;
                                    }
                                });
                                return;
                            }
                        });
                }

                $scope.getImageSrc = function (goods_id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == goods_id) {
                            src = image.preview;
                        }
                    });
                    return src;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_slider_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.sliders, function (index, slider) {
                                    if (slider.id == id) {
                                        $scope.sliders.splice(index, 1);
                                        return false;
                                    }
                                });
                                return;
                            }
                        });
                }

            }])
    .controller('SliderEditCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice', 'dfImage',
            function ($scope, $http, dfLoading, $window, dfNotice, dfImage) {
                $scope.slider = {};
                $scope.image = null;

                $scope.init = function () {
                    $scope.slider = $window._slider;
                    $scope.image = $window._image;
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_slider_data?action=save]', {
                        slider: $scope.slider
                    })
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                var url = '[link:admin_slider?action=edit]?id=' + response.slider.id;
                                window.location.href = url;
                            }
                        });
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\Slider',
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
    .controller('MenuListController',
        ['$scope', '$http', '$window', 'dfLoading', 'dfImage', 'dfNotice',
            function ($scope, $http, $window, dfLoading, dfImage, dfNotice) {
                $scope.menus = [];
                $scope.codes = [];
                $scope.images = [];
                $scope.types = [];
                $scope.show_menus = [];
                $scope.select_menus = [];
                $scope.form = {type: 1, pid: 0};
                $scope.tmp = [];
                $scope.select_code = null;

                $scope.init = function () {
                    $scope.menus = $window._menus;
                    $scope.types = $window._types;
                    $scope.images = $window._images;
                }

                $scope.show_menu = function (menu) {
                    if (menu.pid == 0) {
                        return true;
                    }
                    if ($scope.show_menus[menu.pid]) {
                        return true;
                    }
                    return false;
                }

                $scope.show_menu_child = function (menu) {
                    if ($scope.show_menus[menu.id]) {
                        delete($scope.show_menus[menu.id]);
                        delete($scope.select_menus[menu.id]);
                    }
                    else {
                        $scope.show_menus[menu.id] = true;
                        if ($scope.has_child(menu)) {
                            $scope.select_menus[menu.id] = true;
                        }
                    }
                }

                $scope.is_selected = function (menu) {
                    if ($scope.select_menus[menu.id] != null) {
                        return true;
                    }
                    return false;
                }

                $scope.has_child = function (menu) {
                    if (menu.children != 0) {
                        return true;
                    }
                    return false;
                }

                $scope.getMenus = function (pid) {
                    var menus = [];
                    $.each($scope.menus, function (index, menu) {
                        if (menu.pid == pid) {
                            menus.push(menu);
                        }
                    });
                    return menus;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_menu?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.menus, function (index, menu) {
                                    if (menu.id == id) {
                                        $scope.menus.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }

                $scope.up = function (menu) {
                    $scope.ajaxChangePos(menu.id, menu.pos, 'up');
                }

                $scope.down = function (menu) {
                    $scope.ajaxChangePos(menu.id, menu.pos, 'down');
                }

                $scope.change_pos = function (menu) {
                    $scope.ajaxChangePos(menu.id, menu.pos, 'edit');
                }

                $scope.ajaxChangePos = function (id, pos, type) {
                    dfLoading.loading();
                    $http.post('[link:admin_menu_data?action=changePos]', {id: id, pos: pos, type: type})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $scope.menus = response.menus;
                                if ($scope.select_code) {
                                    $scope.tmp = $scope.menus;
                                    $scope.menus = [];
                                    $.each($scope.tmp, function (index, menu) {
                                        if (menu.code == $scope.select_code) {
                                            $scope.menus.push(menu);
                                        }
                                    });
                                }
                            }
                        });
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_menu_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.menus, function (index, menu) {
                                    if (menu.id == id) {
                                        $scope.menus[index].status = status;
                                        return false;
                                    }
                                });
                                return;
                            }
                        });
                }


                $scope.deleteImage = function (id) {

                    var image_id = null;
                    var _index = null;
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            _index = index;
                            image_id = image.image_id;
                        }
                    });

                    if (image_id) {
                        dfImage.delete(image_id, function () {
                            $scope.images.splice(_index, 1);
                        });
                    }

                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\Config\\Menu',
                            function (data, status, headers, config) {
                                dfLoading.ready();
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

                $scope.getImageSrc = function (id) {
                    var src = '/source/images/no.png';
                    $.each($scope.images, function (index, image) {
                        if (image.target_id == id) {
                            src = image.preview;
                        }
                    });
                    return src;
                }


                $scope.select = function (code) {
                    if (code) {
                        $scope.tmp = $scope.menus;
                        $scope.select_code = code;
                        $scope.menus = [];
                        $.each($scope.tmp, function (index, menu) {
                            if (menu.code == code) {
                                $scope.menus.push(menu);
                            }
                        });
                    } else {
                        $scope.menus = $scope.tmp;
                        $scope.select_code = null;
                    }
                }

                $scope.add = function (id) {
                    var form = {type: 1, pos: 0};
                    $.each($scope.menus, function (index, menu) {
                        if (menu.id == id) {
                            form.code = menu.code;
                            form.pid = menu.id;
                            return false;
                        }
                    });
                    $scope.form = form;
                    $.magnificPopup.open({
                        items: {
                            src: '#form'
                        },
                        type: 'inline'
                    }, 0);
                }

                $scope.addForm = function () {
                    var form = {type: 1, pos: 0, code: $scope.select_code};
                    $scope.form = form;
                    $.magnificPopup.open({
                        items: {
                            src: '#form'
                        },
                        type: 'inline'
                    }, 0);
                }

                $scope.edit = function (id) {
                    $.each($scope.menus, function (index, menu) {
                        if (menu.id == id) {
                            $scope.form = menu;
                            return false;
                        }
                    });
                    $.magnificPopup.open({
                        items: {
                            src: '#form'
                        },
                        type: 'inline'
                    }, 0);
                }

                $scope.save = function () {
                    dfLoading.loading();
                    $http.post('[link:admin_menu_data?action=save]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.menus = response.menus;
                                if ($scope.select_code) {
                                    $scope.tmp = $scope.menus;
                                    $scope.menus = [];
                                    $.each($scope.tmp, function (index, menu) {
                                        if (menu.code == $scope.select_code) {
                                            $scope.menus.push(menu);
                                        }
                                    });
                                }
                                $.magnificPopup.close();
                            }
                        });
                }

            }])
    .controller('ImagesListCtrl',
        ['$scope', '$http', '$window', 'dfLoading', 'dfImage', 'dfNotice',
            function ($scope, $http, $window, dfLoading, dfImage, dfNotice) {
                $scope.images = [];
                $scope.types = [];
                $scope.get = {};

                $scope.init = function () {
                    $scope.images = $window._images;
                    $scope.types = $window._types;
                    $scope.get = $window._get;
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

                    var url = '[link:admin_image?action=list]' + get;
                    window.location.href = url;
                };

                $scope.cancel = function () {
                    var url = '[link:admin_image?action=list]';
                    window.location.href = url;
                }

                $scope.up = function (image) {
                    $scope.ajaxChangePos(image.image_id, image.pos, 'up');

                }

                $scope.down = function (image) {
                    $scope.ajaxChangePos(image.image_id, image.pos, 'down');
                }

                $scope.change_pos = function (image) {
                    $scope.ajaxChangePos(image.image_id, image.pos, 'edit');
                }


                $scope.ajaxChangePos = function (id, pos, type) {
                    dfLoading.loading();
                    dfImage.priority(id, pos, type, function (response) {
                        dfLoading.ready();
                        if (response.error) {
                            dfNotice.error(response.error);
                            return;
                        }
                        if (response.ok) {
                            $.each($scope.images, function (index, item) {
                                if (item.image_id == id) {
                                    $scope.images[index] = response.image;
                                }
                            });
                        }
                    });

                }

                $scope.main = function (image, status) {
                    dfLoading.loading();
                    dfImage.main(image.image_id, status, function () {
                        dfLoading.ready();
                        $.each($scope.images, function (index, item) {
                            if (item.image_id == image.image_id) {
                                $scope.images[index].main = status;
                            } else if (
                                item.target_id == image.target_id &&
                                item.target_type == image.target_type
                            ) {
                                if (status == 1) {
                                    $scope.images[index].main = 0;
                                }
                            }
                        });
                    });

                }


                $scope.delete = function (item) {
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

                $scope.save = function (image) {
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

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.update(file, id,
                            function (data, status, headers, config) {
                                dfLoading.ready();
                                if (data.errors) {
                                    dfNotice.errors(data.errors);
                                }
                                else {
                                    var select = false;
                                    $.each($scope.images, function (index, image) {
                                        if (image.id == data.id) {
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
    .controller('ImagesManagerCtrl',
        ['$scope', '$http', '$window', 'dfLoading', 'dfImage', 'dfNotice',
            function ($scope, $http, $window, dfLoading, dfImage, dfNotice) {
                $scope.images = [];
                $scope.types = [];
                $scope.get = {};

                $scope.init = function () {
                    $scope.images = $window._images;
                    $scope.types = $window._types;
                    $scope.get = $window._get;
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

                    var url = '[link:admin_image?action=manager]' + get;
                    window.location.href = url;
                };

                $scope.cancel = function () {
                    var url = '[link:admin_image?action=manager]';
                    window.location.href = url;
                }

                $scope.delete = function (item) {
                    if (!confirm('Вы действительно хотите удалить изображение?')) {
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

                $scope.select = function (image) {
                    window.opener.CKEDITOR.tools.callFunction($scope.get.CKEditorFuncNum, image.normal);
                    window.close();
                };

            }])
    .controller('ImageEditCtrl',
        ['$scope', '$http', '$window', 'dfLoading', 'dfImage', 'dfNotice',
            function ($scope, $http, $window, dfLoading, dfImage, dfNotice) {
                $scope.image = {};
                $scope.coords = {resize: "1"};

                $scope.init = function (target) {
                    $scope.image = $window._image;

                    if ($scope.image.pre_width == 0) {
                        $scope.refresh(true);
                    }

                    $(function () {
                        $(target).Jcrop({
                            onChange: showCoords,
                            onSelect: showCoords
                        });

                    });
                };

                $scope.save = function () {
                    dfLoading.loading();
                    dfImage.save($scope.image,
                        function (response) {
                            window.location.reload(true);
                        });
                }

                $scope.onFileSelect = function ($files) {
                    if (!confirm('Вы действительно хотите заменить изображение?')) {
                        return false;
                    }
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.update(file, $scope.image.id,
                            function (data, status, headers, config) {
                                dfLoading.ready();
                                if (data.errors) {
                                    dfNotice.errors(data.errors);
                                }
                                else {
                                    window.location.reload();
                                }
                            });
                    }
                };

                $scope.delete = function () {
                    dfLoading.loading();
                    dfImage.delete($scope.image.id,
                        function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            $window.history.back();
                        });
                };

                $scope.refresh = function (reload) {
                    dfImage.refresh($scope.image.image_id,
                        function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }
                            if (response.ok) {
                                if (reload) {
                                    window.location.reload();
                                }
                                dfNotice.ok(response.ok);
                                $scope.image = response.image;
                                return;
                            }
                        });

                }

                $scope.crop = function () {
                    dfLoading.loading();
                    dfImage.crop($scope.image.image_id, $scope.coords,
                        function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.image = response.image;
                                return;
                            }
                        });
                }

                function showCoords(c) {
                    $('#x').val(c.x).trigger('change');
                    $('#y').val(c.y).trigger('change');
                    $('#x2').val(c.x2).trigger('change');
                    $('#y2').val(c.y2).trigger('change');
                    $('#w').val(c.w).trigger('change');
                    $('#h').val(c.h).trigger('change');
                };


            }])
    .controller('QuestionsListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.questions = [];

                $scope.init = function () {
                    $scope.questions = $window._questions;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_question_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.questions, function (index, question) {
                                    if (question.id == id) {
                                        $scope.questions.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_question_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.questions, function (index, question) {
                                    if (question.id == id) {
                                        $scope.questions[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }


            }])
    .controller('QuestionController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.question = {};

                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('answer');

                    $scope.question = $window._question;
                }

                $scope.save = function () {
                    $scope.question.answer = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_question_data?action=save]', {question: $scope.question})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors);
                                return;
                            }

                            if (response.ok) {
                                var href = '[link:admin_question?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });

                };

            }])
    .controller('VideoListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.videos = [];
                $scope.categories = [];
                $scope.form = {};

                $scope.init = function () {
                    $scope.videos = $window._videos;
                    $scope.categories = $window._categories
                }

                $scope.getNameCat = function (cid) {
                    var name = '';
                    $.each($scope.categories, function (index, item) {
                        if (item.value == cid) {
                            name = item.name;
                        }
                    });

                    return name;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.add = function () {
                    if (!$scope.form.url) {
                        dfNotice.error('Укажите URL видео');
                        return;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_video_data?action=add]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                var href = '[link:admin_video?action=edit]?id=' + response.id;
                                window.location.href = href;
                            }
                        });
                }

                $scope.status = function (id, status) {
                    dfLoading.loading();
                    $http.post('[link:admin_video_data?action=status]', {id: id, status: status})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.videos, function (index, video) {
                                    if (video.id == id) {
                                        $scope.videos[index].status = status;
                                        return;
                                    }
                                });
                            }
                        });
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_video_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                dfNotice.error(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.videos, function (index, video) {
                                    if (video.id == id) {
                                        $scope.videos.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                }


            }])
    .controller('VideoController',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.video = {};
                $scope.image = null;
                $scope.meta = {};
                $scope.categories = [];
                $scope.tags = [];
                $scope.ckeditor = null;

                $scope.init = function () {
                    $scope.ckeditor = CKEDITOR.replace('text');

                    $scope.categories = $window._categories;
                    $scope.video = $window._video;
                    $scope.meta = $window._meta;
                    $scope.image = $window._image;
                    $scope.tags = $window._tags;
                }

                $scope.show_cat = function () {
                    var i = 0;
                    $.each($scope.categories, function () {
                        if (i == 1) {
                            i++;
                            return
                        }
                        i++;
                    });
                    if (i == 1) {
                        return false;
                    } else {
                        return true;
                    }
                }

                $scope.save = function () {
                    $scope.video.text = $scope.ckeditor.getData();
                    dfLoading.loading();
                    $http.post('[link:admin_video_data?action=save]', {
                        video: $scope.video,
                        meta: $scope.meta,
                        tags: $scope.tags
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
                }

                $scope.onFileSelect = function ($files, id) {
                    for (var i = 0; i < $files.length; i++) {
                        var file = $files[i];
                        dfLoading.loading();
                        dfImage.set(file, id,
                            '\\CMS\\Core\\Entity\\Video',
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
    .controller('TagsListCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfNotice) {
                $scope.tags = [];
                $scope.tables = [];
                $scope.get = {};

                $scope.init = function () {
                    $scope.tags = $window._tags;
                    $scope.tables = $window._tables;
                    $scope.get = $window._get;
                }

                $scope.delete = function (id) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    dfLoading.loading();
                    $http.post('[link:admin_tags_data?action=delete]', {id: id})
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.error) {
                                alert(response.error);
                                return;
                            }
                            if (response.ok) {
                                $.each($scope.tags, function (index, tag) {
                                    if (tag.tag_id == id) {
                                        $scope.tags.splice(index, 1);
                                        return false;
                                    }
                                });
                                dfNotice.ok(response.ok);
                                return;
                            }
                        });
                };


                $scope.status = function (item, status) {
                    item.status = status;
                    $scope.save(item);
                }


                $scope.save = function (tag) {

                    dfLoading.loading();
                    $http.post('[link:admin_tags_data?action=save]', {
                        tag: tag

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
                };

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

                    var url = '[link:admin_tags?action=list]' + get;
                    window.location.href = url;
                };

                $scope.cancel = function () {
                    var url = '[link:admin_tags?action=list]';
                    window.location.href = url;
                }

            }])
    .controller('TagsCtrl',
        ['$scope', '$http', 'dfLoading', '$window', 'dfImage', 'dfNotice',
            function ($scope, $http, dfLoading, $window, dfImage, dfNotice) {
                $scope.tag = {};
                $scope.meta = {};

                $scope.init = function () {
                    $scope.tag = $window._tag;
                    $scope.meta = $window._meta;
                }

                $scope.save = function () {

                    dfLoading.loading();
                    $http.post('[link:admin_tags_data?action=save]', {
                        tag: $scope.tag,
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
                            }
                        });
                }

            }])
    .controller('RedirectListCtrl',
        ['$scope', '$window', 'dfNotice', 'dfLoading', '$http',
            function ($scope, $window, dfNotice, dfLoading, $http) {
                $scope.redirects = [];
                $scope.moves = [];
                $scope.paths = [];
                $scope.form = {type_url: 1, type_move: 1, pos: 0};

                $scope.init = function () {
                    $scope.redirects = $window._redirects;
                    $scope.moves = $window._moves;
                    $scope.paths = $window._paths;
                }

                $scope.edit = function (redirect) {
                    $http.post('[[link:admin_redirect_data?action=save]]', redirect)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok)
                            }
                        });
                }

                $scope.delete = function (redirect) {
                    if (!confirm('Вы действительно хотите удалить?')) {
                        return false;
                    }
                    $http.post('[[link:admin_redirect_data?action=delete]]', redirect);
                    $.each($scope.redirects, function (index, item) {
                        if (item.id == redirect.id) {
                            $scope.redirects.splice(index, 1);
                            return false;
                        }
                    });
                }

                $scope.add = function () {
                    dfLoading.loading();
                    $http.post('[[link:admin_redirect_data?action=save]]', $scope.form)
                        .success(function (response) {
                            dfLoading.ready();
                            if (response.errors) {
                                dfNotice.errors(response.errors)
                            }
                            if (response.ok) {
                                dfNotice.ok(response.ok);
                                $scope.form = {type_url: 1, type_move: 1, pos: 0};
                                $scope.redirects.unshift(response.redirect);
                            }

                        });
                }


            }]);
