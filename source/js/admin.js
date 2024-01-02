$(function () {
    $('.select2').select2({placeholder: "Select a State", maximumSelectionSize: 6});

    $('.open-popup').magnificPopup({
        type: 'inline',
        midClick: true
    });

    updateSelect();
    setTimeout(function () {
        $('.before_select2').select2({placeholder: "Select a State", maximumSelectionSize: 10});
    }, 100);

    $().UItoTop({easingType: 'easeOutQuart'});

    $('#myTab').tab();

    $('.image-link').magnificPopup({
        type: 'image',
        gallery: {
            enabled: true
        },
        image: {
            titleSrc: 'title',
        }
    });

    $('.gallery').each(function () { // the containers for all your galleries
        $(this).magnificPopup({
            delegate: 'a', // the selector for gallery item
            type: 'image',
            gallery: {
                enabled: true
            },
            image: {
                titleSrc: 'title',
            }
        });
    });


    $('.popup-link-ajax').magnificPopup({
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

});

function updateSelect() {
    setTimeout(function () {
        $('.before_select_value,.select_update').each(function () {
            var value = $(this).data('value');
            $("option[value='" + value + "']", this).attr("selected", "selected");
        });
    }, 50);
}

function truncate(str, maxlength) {
    if (str.length > maxlength) {
        return str.slice(0, maxlength - 1) + '\u2026';
    }
    return str;
};

function generatorHideForm(action, method, input) {
    'use strict';
    var form;
    form = $('<form />', {
        action: action,
        method: method,
        style: 'display: none;'
    });
    if (typeof input !== 'undefined' && input !== null) {
        $.each(input, function (name, value) {
            $('<input />', {
                type: 'hidden',
                name: name,
                value: value
            }).appendTo(form);
        });
    }
    form.appendTo('body').submit();
};

// #NK
function load_ajax_popup(url){
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
    }, 0);
};

