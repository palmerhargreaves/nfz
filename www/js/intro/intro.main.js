$(function(){
    
    $("#intro-modal .continue-button").click(function(){
        $("#intro-modal").krikmodal('hide');
       introJs().start(); 
    });
    
    $("#intro-complete-modal .continue-button").click(function(){
        $("#intro-complete-modal").krikmodal('hide');
    });

    
    $("#user-messages")
        .data("step", 1)
        .data("intro", "При поступлении новых сообщений от&nbsp;импортера или агентства здесь будут отражаться уведомления, при нажатии на&nbsp;которые Вы&nbsp;сможете перейти к&nbsp;конкретной активности.");

    $("#user-menu")
        .data("step", 2)
        .data("intro", "Здесь Вы можете редактировать личные данные, сменить пароль или удалить старых пользователей.");
        
    $("#chat-button")
        .data("step", 3)
        .data("intro", "У Вас возник вопрос по пользованию ресурсом? Не знаете, как корректно заполнить ту или иную активность? Напишите нашему менеджеру, что Вы хотели бы узнать о работе с порталом.")
        .data("position", "left");
        
    $(".budget-wrapper")
        .data("step", 4)
        .data("intro", "Здесь представлена функциональная область, показывающая запланированную сумму инвестиций в маркетинг сервиса на текущий год. При нажатии на определенный квартал Вы видите временную шкалу, отражающую текущий процент освоения планового бюджета. <br /><br />Внимание! Фактическая сумма маркетинговых инвестиций в 1-3 кварталах должна быть не менее запланированной.")
        .data("position", "bottom");

    $(".activities-list")
        .data("step", 5)
        .data("intro", "Здесь представлен перечень обязательных дилерских активностей, каждая из&nbsp;которых отражает определенную сторону вовлеченности дилерского предприятия в&nbsp;маркетинговую политику сервиса марки Volkswagen PKW. Активности пронумерованы и&nbsp;имеют срок исполнения.<br /><br />Наиболее приоритетные активности отмечены знаком восклицания.<br /><br />Внимание! В&nbsp;квартал необходимо закрывать не&nbsp;менее трех обязательных дилерских активностей.")
        .data("position", "top")
        .data("no-scroll", true);
        
    $("a.activity:contains('Собственные дилерские активности')")
        .data("step", 6)
        .data("intro", "Здесь необходимо размещать только те заявки, которые не относятся ни к одной из вышеперечисленных обязательных дилерских активностей.")
        .data("position", "top");
        
    $("a.activity:contains('Первичная идентификация дилера')")
        .data("step", 7)
        .data("intro", "Давайте рассмотрим активность подробнее на примере.");

        
    $("body")
        .data("step", 8)
        .data("intro", "-")
        .data("next-page", $("a.activity:contains('Первичная идентификация дилера')").attr('href') + "?intro");
        
        
    if (RegExp('intro', 'gi').test(window.location.search)) {
        $("#intro-modal").krikmodal("hide");
        introJs().start();
    }
    
    if (RegExp('tour-complete', 'gi').test(window.location.search)) {
        $("#intro-complete-modal").krikmodal('show');
    }
    
    if (RegExp('start-tour', 'gi').test(window.location.search)) {
        $("#intro-modal").krikmodal('show');
    }

    if (RegExp('service', 'gi').test(window.location.search)) {
        $("#spring-service-action-modal").krikmodal('show');
    }
    
    /*$("#txtSpecialBudget1Q").focus();
    $("#txtSpecialBudget1Q, #txtSpecialBudgetSumm").live('input', function(e) {
        if(!checkInt($(this).val())) {
            $('.special-budget-form-eror-msg').fadeIn();
            return;
        }
        $('.special-budget-form-eror-msg').fadeOut();

        var val1 = parseFloat($("#txtSpecialBudget1Q").val()), val2 = parseFloat($("#txtSpecialBudgetSumm").val()),
                percent = (val2 / val1) * 100;

        if(percent < 120) {
            $('.special-budget-msg').fadeIn();
            $('.accept-button').attr('disabled', true).addClass('gray');
        }
        else {
            $('.accept-button').removeAttr('disabled').removeClass('gray');
            $('.special-budget-msg').fadeOut();
        }

    });

    $(".accept-button").live('click', function(e) {
		e.preventDefault();
		
        $.post('/home/specialAccept',
                {
                    act : 'accept',
                    userId : $(this).data('id'),
                    budget : $('#txtSpecialBudget1Q').val(),
                    sum : $('#txtSpecialBudgetSumm').val()
                }, function(result) {
                    $("#special-budget-modal").krikmodal('hide');
					window.location.reload();
            });
    });

    $('.decline-button').live('click', function(e) {
        e.preventDefault();

         $.post('/home/specialAccept',
                {
                    act : 'decline',
                    userId : $(this).data('id')
                }, function(result) {
                    $("#special-budget-modal").krikmodal('hide');
					window.location.reload();
            });
    });
    
    var checkInt = function(val) {
        return new RegExp(/^[0-9.]+$/).test(val);
    }*/

    

})