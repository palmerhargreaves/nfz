$(function(){
    
    $(".content-wrapper")
        .data("step", 1)
        .data("intro", "Здесь Вы согласовываете макеты по активности нажатием на кнопку «Добавить макет».")
        .data("position", "top")
        .data("no-scroll", true);
        
    $("#add-model-button")
        .data("step", 2)
        .data("intro", "Рассмотрим окно заявки");

    $("#model")
        .data("step", 3)
        .data("intro", "Окно заявки содержит три вкладки.<br /><br />Вкладка &laquo;Материал&raquo; предназначена для загрузки и&nbsp;согласования рекламных материалов в&nbsp;рамках данной активности.<br /><br />Вкладка &laquo;Отчет&raquo; становится активной после получения подтверждения от&nbsp;импортера о&nbsp;размещении заявленного макета. Сюда Вы&nbsp;загружаете документы, подтверждающие размещение данного макета (фотоотчет, финансовые документы, скриншоты веб-страниц и&nbsp;т.п.).<br /><br />Во&nbsp;вкладке &laquo;Статус&raquo; можно оперативно решить возникающие вопросы, а&nbsp;также отследить состояние данной заявки.")
        .data("position", "left")
        .data("no-scroll", true);
        
    $("body")
        .data("step", 4)
        .data("intro", "-")
        .data("next-page", $("#header a.logo").attr('href') + "?tour-complete");

    function onchange(targetElement) {  
        if($(targetElement).hasClass("modal")) {
            $("#model").removeClass("introjs-fixParent");
            $("#add-model-button").trigger("click");
        }
            
    };
    
    
    if (RegExp('intro', 'gi').test(window.location.search)) {
        introJs().onchange(onchange).start();
    }
})


