$(function() {
	$('.news-status-change').click(function() {
		var $el = $(this);

		$.post($el.data('url'), {}, function(result) {
			if($el.hasClass('news-active')) {
				$el.removeClass('news-active').addClass('news-inactive');
			}
			else {
				$el.removeClass('news-inactive').addClass('news-active');
			}
		});
	});
});