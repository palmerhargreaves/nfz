$(function(){
    
    $(".content-wrapper")
        .data("step", 1)
        .data("intro", "Здесь представлены все разработанные импортером рекламные материалы по данной активности, которые Вы можете адаптировать исходя из потребностей Вашего дилерского предприятия.")
        .data("position", "top")
        .data("no-scroll", true);
        
    $("#agreement-tab")
        .data("step", 2)
        .data("intro", "Перейдем во вкладку «Согласование»")
        .data("position", "top");

        
    $("body")
        .data("step", 3)
        .data("intro", "-")
        .data("next-page", $("#agreement-link").attr('href') + "?intro");
           
    if (RegExp('intro', 'gi').test(window.location.search)) {
        introJs().start();
    }
})