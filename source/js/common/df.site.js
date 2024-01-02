if (typeof  df === 'undefined' || !df.init_common) {
    alert('need include df.js for df.mobile.js');
}

df.init_site = true;

df.ajaxPopup = function (url, data, callback) {
    const instance = $.magnificPopup.instance;
    instance.open({
        items: {
            src: url,
            type: 'ajax'
        },
        ajax: {
            settings: {
                type: 'POST',
                data: data || {}
            }
        },
        callbacks: {
            beforeAppend: function () {
                if (!instance.waitingForAjax) {
                    instance.content = [$('<div/>')];
                }
                instance.waitingForAjax = false;
            },
            parseAjax: function (mfpResponse) {
                instance.currTemplate = {'ajax': true};
                instance.waitingForAjax = true;
                if (mfpResponse.xhr.responseJSON.error) {
                    console.log(mfpResponse.xhr.responseJSON.error);
                    this.close();
                    return;
                }
                mfpResponse.data = mfpResponse.xhr.responseJSON.html;
            },
            ajaxContentAdded: function () {
                var $div = this.content;
                if ($div.length) {
                    if (typeof angular !== "undefined") {
                        angular.element(document).injector().invoke(function ($compile) {
                            var scope = angular.element($div).scope();
                            $compile($div)(scope);
                        });
                    }
                } else {
                    this.close();
                }
                if (callback) {
                    callback(this);
                }

            }
        }
    });
    instance.ev.off('mfpBeforeChange.ajax');
};

df.addLinkCopy = function (copy) {
    return function () {
        var body_element = document.getElementsByTagName('body')[0];
        var selection;
        selection = window.getSelection();
        var pagelink = "<div style='position: absolute;left:-99999px; '><br /><br /> Источник: <a href='" + document.location.href + "'>" + document.title + "</a><br />" + copy + "</div>"; // В этой строке поменяйте текст на свой
        var copytext = selection + pagelink;
        var p = document.createElement('p');
        body_element.appendChild(p);
        p.innerHTML = copytext;
        selection.selectAllChildren(p);
        window.setTimeout(function () {
            body_element.removeChild(p);
        }, 0);
    }
};


df.getCookie = function (name) {
    var tmp = document.cookie.split('; ');
    var i;
    var arr = [];
    var tt = [];

    for (i in tmp) {
        if (!tmp[i].substring) continue;

        var eqIndex = tmp[i].indexOf("=");
        var cookieName = tmp[i].substring(0, eqIndex);
        var cookieValue = tmp[i].substring(eqIndex + 1);
        arr[cookieName] = cookieValue;
    }

    if (name != '' && name != ' undefined' && name != null) {
        if (typeof(arr[name]) != 'undefined')
            return arr[name];
        else
            return null;
    }
    else return arr;
};


df.setCookie = function (name, value, expires, path, domain, secure) {
    var host = location.host.split(".");
    var d = new Date();
    if (typeof expires === 'number') {
        d.setMilliseconds(d.getMilliseconds() + expires * 864e+5);
        expires = d;
    } else {
        expires = new Date(d.getFullYear() + 6, 10, 10, 10, 10, 10);
    }

    document.cookie = name + "=" + escape(value) +
        ((expires) ? "; expires=" + expires : "") +
        ((path) ? "; path=" + path : "; path=/") +
        ((domain) ? "; domain=" + domain : "; domain=" + (host[host.length - 2] + "." + host[host.length - 1])) +
        ((secure) ? "; secure" : "");
};

df.generatorHideForm = function (action, method, input) {
    'use strict';
    var form;
    form = $('<form />', {
        action: action,
        method: method,
        style: 'display: none;'
    });
    if (typeof input !== 'undefined' && input !== null) {
        $.each(input, function (name, value) {
            console.log(name);
            console.log(value);
            $('<input />', {
                type: 'hidden',
                name: name,
                value: value
            }).appendTo(form);
        });
    }
    form.appendTo('body').submit();
};
/*
df.getFormData = function ($form) {
    console.log($form);
    var data = $form.serializeArray();
    var array = {};

    $.map(data, function (n, i) {
        array[n['name']] = n['value'];
    });

    return array;
}*/