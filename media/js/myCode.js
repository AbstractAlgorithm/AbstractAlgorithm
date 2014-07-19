$(document).ready(function() {
    var header = $("#header"),
        offset = 20;

    $(window).scroll(function() {
        header.css
                (
                    'top',
                    ($(window).scrollTop() <= 77-offset
                    ? (77-$(window).scrollTop())
                    : offset) +"px"
                );
    });

    // $('#vote').click(function() {

    // });
});