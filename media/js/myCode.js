$(document).ready(function() {
    var header = $("#header"),
        offset = 20;

    $(window).scroll(function() {
        header.css
                (
                    'top',
                    ($(window).scrollTop() <= 30-offset
                    ? (30-$(window).scrollTop())
                    : offset) +"px"
                );
    });

    // $('#vote').click(function() {

    // });
});