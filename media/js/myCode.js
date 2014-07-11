window.onload = function() {
    var header = document.getElementById("header"),
        cont   = document.getElementById("content");


    window.onscroll = function() {
        var offset = 20;
        header.style.top   = window.scrollY<=82-offset
                                ? (82-window.scrollY)+"px"
                                : offset+"px";
    };

    $('#vote').click(function() {

    });
};