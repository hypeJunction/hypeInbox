define(['jquery', 'elgg'], function ($, elgg, ui) {

	var inbox = {
		/**
		 * Bind events
		 * @returns {void}
		 */
		init: function () {
			if (elgg.config.inboxUser) {
				return;
			}
			$(document).on('click', '.inbox-message[data-href]', inbox.navigateToMessage);
			$(document).on('click', '#inbox-form-toggle-all', inbox.toggleAll);
			$(document).on('click', '[data-submit]', inbox.submitBulkForm);
			$(document).on('click', '.elgg-menu-item-actions > a', inbox.showChildMenu);
			$(document).on('click', '*', inbox.hideChildMenu);
			$(document).on('click', '.elgg-menu-item-delete > a', inbox.deleteMessage);
			$(document).on('click', '.elgg-menu-item-markread > a', inbox.markMessageAsRead);
			$(document).on('click', '.elgg-menu-item-markunread > a', inbox.markMessageAsUnread);
			$(document).on('click', '.inbox-thread-load-before, .inbox-thread-load-after', inbox.threadLoadMore);
			elgg.config.inboxUser = true;
		},
		navigateToMessage: function (e) {
			if (!$(e.target).parents().andSelf().is('a,input,.elgg-menu')) {
				e.preventDefault();
				location.href = $(this).data('href');
			}
		},
		toggleAll: function (e) {
			var prop = $(this).prop('checked');
			$(this).closest('form').find('[type="checkbox"][name="guids[]"]').prop('checked', prop);
		},
		submitBulkForm: function (e) {
			var $elem = $(this);
			if ($elem.data('confirm')) {
				if (!confirm($elem.data('confirm'))) {
					return false;
				}
			}
			var $form = $elem.closest('form');
			if ($form.length === 0) {
				return;
			}
			e.preventDefault();

			$form.attr('action', $elem.attr('href')).trigger('submit');
		},
		showChildMenu: function (e) {
			if ($(e.target).closest('.inbox-menu').length === 0) {
				return;
			}

			e.preventDefault();

			var $anchor = $(this);
			var $item = $anchor.parent('li');
			var $menu = $(this).data('menu') || null;

			if (!$menu) {
				$menu = $item.children('.elgg-child-menu').eq(0);
				$(this).data('menu', $menu);
			}

			if ($menu.is(':visible')) {
				$menu.hide();
			} else {
				$menu.show().position({
					my: $menu.data('my') || 'right top',
					at: $menu.data('at') || 'right bottom',
					of: $item,
				});
			}

			$('.inbox-menu .elgg-child-menu:visible').not($menu).hide();
		},
		hideChildMenu: function (e) {
			if ($(e.target).parents('.inbox-menu').length === 0) {
				$('.elgg-child-menu:visible').hide();
			}
		},
		deleteMessage: function (e) {
			if ($(e.target).closest('.elgg-item-object-messages').length === 0) {
				return;
			}

			var $elem = $(this);

			if ($elem.data('confirm')) {
				if (!confirm($elem.data('confirm'))) {
					return false;
				}
			}

			e.preventDefault();

			elgg.action($elem.attr('href'), {
				beforeSend: function () {
					$('body').addClass('elgg-state-loading');
					$('.inbox-menu .elgg-child-menu:visible').hide();
				},
				complete: function () {
					$('body').removeClass('elgg-state-loading');
				},
				success: function (data) {
					$elem.closest('.elgg-item-object-messages').slideUp().remove();
				}
			});
		},
		markMessageAsRead: function (e) {
			if ($(e.target).closest('.elgg-item-object-messages').length === 0) {
				return;
			}

			e.preventDefault();

			var $elem = $(this);

			elgg.action($elem.attr('href'), {
				beforeSend: function () {
					$('body').addClass('elgg-state-loading');
					$('.inbox-menu .elgg-child-menu:visible').hide();
				},
				complete: function () {
					$('body').removeClass('elgg-state-loading');
				},
				success: function (data) {
					$elem.closest('.elgg-item-object-messages').find('[data-read="no"]').attr('data-read', 'yes').data('read', 'yes');
				}
			});
		},
		markMessageAsUnread: function (e) {
			if ($(e.target).closest('.elgg-item-object-messages').length === 0) {
				return;
			}

			e.preventDefault();

			var $elem = $(this);

			elgg.action($elem.attr('href'), {
				beforeSend: function () {
					$('body').addClass('elgg-state-loading');
					$('.inbox-menu .elgg-child-menu:visible').hide();
				},
				complete: function () {
					$('body').removeClass('elgg-state-loading');
				},
				success: function (data) {
					$elem.closest('.elgg-item-object-messages').find('[data-read="yes"]').attr('data-read', 'no').data('read', 'no');
				}
			});
		},
		threadLoadMore: function(e) {
			e.preventDefault();

			var $elem = $(this);

			elgg.get($elem.attr('href'), {
				beforeSend: function () {
					$('body').addClass('elgg-state-loading');
				},
				complete: function () {
					$('body').removeClass('elgg-state-loading');
				},
				success: function (data) {
					$elem.replaceWith($(data));
				}
			});
		}
	};

	inbox.init();
});


