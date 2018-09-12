$(document).ready( function(){

	$(".anons-news .item:even").addClass("even");

	$(".anons-news .more").click( function(){
		heightAnons = $(this).siblings(".content").find(".anons").height()+"px";
		heightFull = $(this).siblings(".content").find(".full").height()+"px";

		if(!$(this).parents(".item").hasClass("active")){
			$(this).parents(".item").animate(
				{
					height: heightFull
				},
				{
					duration: 200,
					specialEasing: {
						height: 'linear'
					}
				});
			$(this).parents(".item").addClass("active");
		}else{
			$(this).parents(".item").animate(
				{
					height: heightAnons
				},
				{
					duration: 200,
					specialEasing: {
						height: 'linear'
					}
				});
			$(this).parents(".item").removeClass("active");
		}
	});

	$("a.tabHeader").live('click', function() {
		$.each($("a.tabHeader"), function(ind, el) {
			if($(el).parent().hasClass('active')) {
				$(el).parent().removeClass('active');
				$('#' + $(el).prop('name')).hide();
			}
		});

		if(!$(this).parent().hasClass('active')) {
			$(this).parent().addClass('active');
			$('#' + $(this).prop('name')).fadeIn();
		}

	});


})