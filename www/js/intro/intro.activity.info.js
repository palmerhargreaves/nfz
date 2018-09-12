$(function(){
    
    $(".activity-header-wrapper")
        .data("step", 1)
        .data("intro", "Для того чтобы активность была зачтена дилерскому предприятию, необходимо выполнить все требования, указанные в&nbsp;данном поле.<br /><br />Внимание! У&nbsp;каждой активности индивидуальный набор требований.");

    $(".content-wrapper")
        .data("step", 2)
        .data("intro", "Для удобства работы над активностью созданы несколько вкладок.<br /><br />Мы находимся в первой вкладке «Информация».")
        .data("position", "top")
        .data("no-scroll", true);
        
    $("#information")
        .data("step", 3)
        .data("intro", "В этой вкладке содержится полная информация (концепция, цель, задачи, пункты, обязательные для исполнения, и&nbsp;т.п.), которая может понадобиться при организации работ в&nbsp;рамках данной активности.")
        .data("position", "top")
        .data("no-scroll", true);

    $("#materials-tab")
        .data("step", 4)
        .data("intro", "Перейдем во вкладку «Материалы»")
        .data("position", "top");
        
    $("body")
        .data("step", 5)
        .data("intro", "-")
        .data("next-page", $("#materials-link").attr('href') + "?intro");
        
    if (RegExp('intro', 'gi').test(window.location.search)) {
        introJs().start();
    }
})