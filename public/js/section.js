var scroll;
if (typeof LocomotiveScroll === "function") {
    scroll = new LocomotiveScroll({
        el: document.querySelector('[data-scroll-container]'),
        smooth: true
    });
}
$( document ).ready(function() {
    scroll.update();
});

$(window).bind("load", function(event) {
    /*url = document.referrer;
    ref = "";
    if(url)
        ref = url.match(/:\/\/(.[^/]+)/)[1];
    if(document.referrer == "" || ref == window.location.hostname)
        $("body").addClass("section-rss");*/

    var currentUrl = window.location.href.split("/");
    var prevUrl = document.referrer.split("/");

    // console.log(currentUrl[2] + " " + prevUrl[2]);

    if(currentUrl[2] != prevUrl[2])
        $("body #main.rss").addClass("section-rss");

    var inPost = false;

    var timeout = setTimeout(function(){
        initSection();
    }, 500);

    function initSection(){
        $("#main").removeClass("hide");
        $("#preloder").addClass("hide");
        scroll.update();
    }

    $('.animate').scrolla({    
        mobile: true,
        once: true,
        animateCssVersion: 3 // used animate.css version (3 or 4)
    });
    
    const animateCSS = (element, animation, prefix = 'animate__') =>
      // We create a Promise and return it
      new Promise((resolve, reject) => {
        const animationName = `${prefix}${animation}`;
        const node = document.querySelector(element);

        node.classList.add(`${prefix}animated`, animationName);

        // When the animation ends, we clean the classes and resolve the Promise
        function handleAnimationEnd(event) {
          event.stopPropagation();
          //node.classList.remove(`${prefix}animated`, animationName);
          resolve('Animation ended');
        }

        node.addEventListener('animationend', handleAnimationEnd, {once: true});
    });

    $("#btn-back a").on("click", function(event) {
        var href = $(this).attr("href");
        var url = $(this).data("url");
        var pageTitle = $(this).data("title");
        var track = $(this).data("track");

        if(inPost){
            /*$("#section-load").removeClass("zoomIn");            
            animateCSS('#section-load', 'zoomOut','');
            timeout = setTimeout(function(){
                location.href = href;
                processAjaxData(pageTitle, url, track);
                $("#section-content").removeClass("d-none zoomOut");
                animateCSS('#section-content', 'zoomIn','');
                $('#section-load').html("");
                if (typeof LocomotiveScroll === "function") { 
                    scroll.update();
                }
            }, 500);*/
            $("#main").addClass("hide");
            timeout = setTimeout(function(){
                location.href = url;
            }, 500);
            inPost = false;
        }else{
            $("#main").addClass("hide");
            timeout = setTimeout(function(){
                location.href = href;
            }, 500);
        }
        
        $(".c-scrollbar").removeClass("d-none");

        return false;
    });

    $(".link-ajax").on("click", function(event) {
        var url = $(this).attr("href") + " #load-content-section";
        var pageTitle = $(this).data("title");
        var track = $(this).attr("href");
        $("#section-content").removeClass("zoomIn");
        $("#section-load").removeClass("zoomOut");
        $(".c-scrollbar").addClass("d-none");
        animateCSS('#section-content', 'zoomOut','');

        timeout = setTimeout(function(){
            $("#section-load").load(url, function(responseTxt, statusTxt, jqXHR){
                if(statusTxt == "success"){
                    inPost = true;
                    $("#section-content").addClass("d-none");
                    animateCSS('#section-load', 'zoomIn','');

                    processAjaxData(pageTitle,url,track);
                    if (typeof LocomotiveScroll === "function") { 
                        scroll.update();
                    }
                }
                if(statusTxt == "error"){
                    alert("Error: " + jqXHR.status + " " + jqXHR.statusText);
                }
            });
        }, 1000);
       
        return false;
    });

    function processAjaxData(title, urlPath, track){
         document.title = title;
         window.history.pushState({"html":urlPath,"pageTitle":title},"", track);
     }

});

//TODO automatic login after active user

$('#btn-loginAuto').click(function (e){
    e.preventDefault();
    var url= $('#url-login').text();
    var token= $('#token-login').text();
    var email= $('#user-email').text();

})
