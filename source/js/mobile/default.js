
(function () {
    $(function () {
        function c() {
            d.removeClass(e);
            a.hide();
            t.show()
            setTimeout(function () {
                f.hide()
            }, g);
            b = !1
        }
        var e = "m-layout__main_menu-open", d = $(".m-layout__main"), f = $(".m-layout__menu-wrap"), h = $(".m-layout__toggle-menu"), a = $(".m-layout__overlay"),t = $(".m-layout__menu-show"), b = !1, g = 400;
        h.on("click", function () {
            b ? c() : (f.show(), d.addClass(e), a.show(),t.hide(), b = !0)
        });
        a.on("click", function () {
            c();
        })
    });
    window.addEventListener("load", function () {
        new FastClick(document.body)
    }, !1)
})();
