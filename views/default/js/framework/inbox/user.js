define(['jquery', 'elgg', 'jquery.form'], function ($, elgg) {

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
			$(document).on('click', '.elgg-menu-item-markread > a', inbox.markMessageAsRead);
			$(document).on('click', '.elgg-menu-item-markunread > a', inbox.markMessageAsUnread);
			$(document).on('submit', '.elgg-form-messages-send', inbox.sendMessage);
			$(document).on('click', '.elgg-menu-inbox li:has(.elgg-child-menu) > a', inbox.toggleChildMenu);
			$(document).on('click', '.inbox-toggle-attachments-form', inbox.toggleAttachmentsForm);
			
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
			$(this).closest('form').find('[type="checkbox"][name="guids[]"]:visible').prop('checked', prop);
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
		markMessageAsRead: function (e) {
			if ($(e.target).closest('.elgg-item-object-messages').length === 0) {
				return;
			}

			e.preventDefault();

			var $elem = $(this);

			elgg.action($elem.attr('href'), {
				beforeSend: function () {
					$('body').addClass('elgg-state-loading');
				},
				complete: function () {
					$('body').removeClass('elgg-state-loading');
				},
				success: function (data) {
					var $msg = $elem.closest('.inbox-message');
					$msg.addClass('inbox-message-read').removeClass('inbox-message-unread');
					var txt = $msg.find('.inbox-message-count-indicator').text();
					$msg.find('.inbox-message-unread-indicator').text(txt);
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
				},
				complete: function () {
					$('body').removeClass('elgg-state-loading');
				},
				success: function (data) {
					var $msg = $elem.closest('.inbox-message');
					$elem.closest('.inbox-message').addClass('inbox-message-unread').removeClass('inbox-message-read');
					var txt = $msg.find('.inbox-message-count-indicator').text();
					$msg.find('.inbox-message-unread-indicator').text(txt);
				}
			});
		},
		sendMessage: function (e) {

			if (!require.defined('hypeList')) {
				return true;
			}

			e.preventDefault();
			require('hypeList');

			var $form = $(this);
			elgg.action($form.attr('action'), {
				data: $form.serialize(),
				beforeSend: function () {
					$form.find('[type="submit"]').prop('disabled', true).addClass('elgg-state-disabled');
					$('body').addClass('elgg-state-loading');
				},
				complete: function () {
					$form.find('[type="submit"]').prop('disabled', false).removeClass('elgg-state-disabled');
					$('body').removeClass('elgg-state-loading');
				},
				success: function (data) {
					if (data.status >= 0) {
						if ($form.closest('#reply').length) {
							$form.resetForm();
							$('.elgg-dropzone-preview', $form).remove();
							$('.inbox-messages').children('.elgg-list').hypeList('fetchNewItems', null, true);
						} else {
							document.location.href = data.forward_url;
						}
					}
				}
			});
		},
		toggleChildMenu: function(e) {
			e.preventDefault();
			$(this).parent().toggleClass('elgg-state-active');
		},
		toggleAttachmentsForm: function(e) {
			e.preventDefault();
			$(this).closest('form').find('.inbox-attachments-form').show();
			$(this).parent().remove();
		}
	};

	inbox.init();
});
