$(function(){
    $(window).on("scroll", ()=>{scroll(window.scrollY)});
    scroll(window.scrollY);
});

function scroll(scrollY){
    $(".title-img").css("top", -scrollY / 2);
    $("h1").css("top", -scrollY / 3 + 100);
}