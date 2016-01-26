define(function (require) {
	var $ = require('jquery');
	var elgg = require('elgg');

	/**
	 * Repositions the popup
	 *
	 * @param {String} hook    'getOptions'
	 * @param {String} type    'ui.popup'
	 * @param {Object} params  An array of info about the target and source.
	 * @param {Object} options Options to pass to
	 *
	 * @return {Object}
	 */
	function popupHandler(hook, type, params, options) {
		if (!params.target.hasClass('elgg-inbox-popup')) {
			return;
		}

		// Due to elgg.ui.popup's design, there's no way to verify in a click handler whether the
		// click will actually be closing the popup after this hook. This is the only chance to verify
		// whether the popup is visible or not.
		if (params.target.is(':visible')) {
			// user clicked the icon to close, we're just letting it close
			return;
		}

		populatePopup();

		options.my = 'left top';
		options.at = 'left bottom';
		options.collision = 'fit none';
		return options;
	}

	/**
	 * Fetch notifications and display them in the popup module.
	 *
	 * @return void
	 */
	function populatePopup() {
		var $loader = $('<div>').addClass('elgg-ajax-loader');

		elgg.action('messages/load', {
			beforeSend: function () {
				$('#inbox-messages').html($loader);
			},
			complete: function () {
				$loader.remove();
			},
			success: function (response) {

				if (response.status !== 0) {
					return;
				}

				// Populate the list
				$('#inbox-messages').html(response.output.list);

				updateUnreadCount(response.output.unread);

				if (typeof elgg.ui.lightbox !== 'undefined') {
					// Bind lightbox to the new links
					elgg.ui.lightbox.bind(".elgg-lightbox");
				}
			}
		});
	}

	/**
	 * Update inbox badge with unread count
	 *
	 * @param {int} unread Unread count
	 * @returns {void}
	 */
	function updateUnreadCount(unread) {
		// Toggle the "Dismiss all" icon
		if (unread > 0) {
			$('#inbox-dismiss-all').removeClass('hidden');
			$('#inbox-new').text(unread).removeClass('hidden');
		} else {
			$('#inbox-dismiss-all').addClass('hidden');
			$('#inbox-new').text(unread).addClass('hidden');
		}
	}

	$(document).ajaxSuccess(function (event, xhr, settings) {
		if (typeof xhr.responseJSON !== 'undefined' && xhr.responseJSON.inbox) {
			updateUnreadCount(xhr.responseJSON.inbox.unread || 0);
		}
	});

	elgg.register_hook_handler('getOptions', 'ui.popup', popupHandler);
});
