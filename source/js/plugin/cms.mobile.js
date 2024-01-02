function initMobilePhone() {
    $div = $('#clbh_phone_div');
    window.onscroll = function () {
        $div.removeClass('cbh-static');
        setTimeout(function () {
            $div.addClass('cbh-static');
        }, 2000)
    }

    $div.hover(function () {
            $div.addClass('cbh-hover');
            $div.removeClass('cbh-static');
        },
        function () {
            $div.removeClass('cbh-hover');
            $div.addClass('cbh-static');
        });

    $(document).on('scroll', function () {
        if ($(document).scrollTop() >= 200) {
            $div.addClass('cbh-green');
        } else {
            $div.removeClass('cbh-green');
        }
    });
}


function initMobileClick() {

    window.ontouchstart = function (e) {
        if (e.target.id !== 'click_tel')
            $("#click_tel").css("display", "none");
    }

    window.ontouchend = function (e) {
        $("#click_tel").fadeIn('slow');
    }


    btnClickPosition();

    function btnClickPosition() {

        var width;
        if (window.innerWidth < 750) {
            width = 50;
        }
        else {
            width = 150;
        }
        $click = $("#click_tel");
        $click.css("width", width);
    }

}