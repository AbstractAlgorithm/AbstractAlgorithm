$(document).ready(function() {
    var header = $("#header"),
        offset = 20;

    $(window).scroll(function() {
        var topPos  = $(window).scrollTop() <= 77-offset
                    ? (77-$(window).scrollTop())
                    : offset;
        header.css('top', topPos+"px");
    });
});