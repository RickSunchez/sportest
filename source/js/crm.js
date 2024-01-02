$(function () {

    $('.ajax-popup-link').magnificPopup({
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
