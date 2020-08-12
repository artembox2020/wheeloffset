head.ready(function() {

	$('.reviews__tabs li').click(function(event) {
		$('.reviews__tabs li').removeClass('is-active');
		$(this).addClass('is-active');
		$('.reviews__container').removeClass('active');
		blocktoshow = $(this).data('block');
		$(blocktoshow).addClass('active');
		return false;
	});

	var agent = navigator.userAgent,
	event = (agent.match(/iPad/i)) ? "touchstart" : "click";

	$(document).bind(event, function(e){
		$(".js-popup").hide();
	});

	console.log($('body').html());
});

