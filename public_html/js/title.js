$(function(){
    window.addEventListener("scroll", throttle(scroll, 10));
    scroll();
});

function scroll(){
    $(".title-img").css("top", -window.scrollY / 4);
}

function throttle (callback, limit) {
    var wait = false;                 // Initially, we're not waiting
    return function () {              // We return a throttled function
        if (!wait) {                  // If we're not waiting
            callback.call();          // Execute users function
            wait = true;              // Prevent future invocations
            setTimeout(function () {  // After a period of time
                wait = false;         // And allow future invocations
            }, limit);
        }
    }
 }