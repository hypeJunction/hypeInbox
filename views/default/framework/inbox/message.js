require(['jquery', 'elgg/ready'], function ($) {
	$(document).on('click', '.inbox-message[data-href]', function (e) {
		if (!$(e.target).parents().andSelf().is('a,input,.elgg-menu')) {
			e.preventDefault();
			location.href = $(this).data('href');
		}
	});
});