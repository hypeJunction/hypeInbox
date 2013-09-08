<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

	elgg.provide('framework.inbox');

	framework.inbox.user = function() {
		$('.inbox-thread-message-summary')
				.live('click', function(e) {
			e.preventDefault();
			var $elem = $(this);
			elgg.ajax($elem.data('href'), {
				beforeSend: function() {
					$elem.addClass('elgg-ajax-loader');
				},
				success: function(data) {
					$elem.replaceWith($(data));
				}
			})
		})

		$('.inbox-thread-load-after')
				.live('click', function(e) {

			e.preventDefault();
			var $elem = $(this);
			var $thread = $('.inbox-messages-thread');

			var data = $thread.data();
			data.notin = $thread.find('article[data-guid]').map(function() {
				return $(this).data('guid')
			}).get().join();

			elgg.ajax($elem.attr('href'), {
				data: data,
				beforeSend: function() {
					$elem.addClass('elgg-state-loading');
				},
				complete: function() {
					$elem.removeClass('elgg-state-loading');
				},
				success: function(data) {
					if (data) {
						$thread.append($(data).html());
					} else {
						$elem.hide();
					}
				}
			})
		})

		$('.inbox-thread-load-before')
				.live('click', function(e) {

			e.preventDefault();
			var $elem = $(this);
			var $thread = $('.inbox-messages-thread');

			var data = $thread.data();
			data.notin = $thread.find('article[data-guid]').map(function() {
				return $(this).data('guid')
			}).get().join();

			elgg.ajax($elem.attr('href'), {
				data: data,
				beforeSend: function() {
					$elem.addClass('elgg-state-loading');
				},
				complete: function() {
					$elem.removeClass('elgg-state-loading');
				},
				success: function(data) {
					if (data) {
						$thread.prepend($(data).html());
					} else {
						$elem.hide();
					}
				}
			})
		})

		$('.inbox-message .elgg-menu-item-delete')
				.live('click', function(e) {

			e.preventDefault();

			var $elem = $(this);
			var confirmText = $(this).find('a').eq(0).data('confirm');

			if (!confirm(confirmText)) {
				return false;
			}

			elgg.action($elem.find('a').eq(0).attr('href'), {
				beforeSend: function() {
					$elem.addClass('elgg-state-loading');
				},
				complete: function() {
					$elem.removeClass('elgg-state-loading');
				},
				success: function(data) {
					$elem.closest('.inbox-message').parent().fadeOut().remove();
				}
			})
		})

		$('.inbox-message .elgg-menu-item-markread')
				.live('click', function(e) {

			e.preventDefault();

			var $elem = $(this);

			elgg.action($elem.find('a').eq(0).attr('href'), {
				beforeSend: function() {
					$elem.addClass('elgg-state-loading');
				},
				complete: function() {
					$elem.removeClass('elgg-state-loading');
				},
				success: function(data) {
					if (data.output.read == data.output.count) {
						$elem.closest('.inbox-message').removeClass('inbox-message-thread-unread');
						$elem.closest('.inbox-message').find('.inbox-message-thread-unread-count').addClass('hidden');
						$elem.hide();
						$elem.siblings('.elgg-menu-item-markunread').show();
					} else {
						$elem.closest('.inbox-message').addClass('inbox-message-thread-unread');
						$elem.closest('.inbox-message').find('.inbox-message-thread-unread-count').removeClass('hidden').text(data.output.count - data.output.read);
						$elem.siblings('.elgg-menu-item-markunread').show();
					}
				}
			})
		})

		$('.inbox-message .elgg-menu-item-markunread')
				.live('click', function(e) {

			e.preventDefault();

			var $elem = $(this);

			elgg.action($elem.find('a').eq(0).attr('href'), {
				beforeSend: function() {
					$elem.addClass('elgg-state-loading');
				},
				complete: function() {
					$elem.removeClass('elgg-state-loading');
				},
				success: function(data) {
					if (data.output.unread > 0) {
						$elem.closest('.inbox-message').addClass('inbox-message-thread-unread');
						$elem.closest('.inbox-message').find('.inbox-message-thread-unread-count').removeClass('hidden').text(data.output.unread);
						$elem.siblings('.elgg-menu-item-markread').show();
					} 
					
					if (data.output.unread == data.output.count) {
						$elem.hide();
					}
				}
			})
		})

	}

	framework.inbox.userpicker = function() {

		$('.userpicker-glossary').live('initialize', framework.inbox.initUserpicker);
		$('[data-glossary]').live('click', framework.inbox.loadUserpickerTab);
		$('.userpicker-glossary').trigger('initialize');

	}

	framework.inbox.initUserpicker = function(e) {

		var $glossary = $(this);

		elgg.post($glossary.data('endpoint'), {
			data: $glossary.data(),
			dataType: 'json',
			beforeSend: function() {
				$glossary.addClass('elgg-state-loading');
			},
			complete: function() {
				$glossary.removeClass('elgg-state-loading');
			},
			success: function(data) {

				$glossary.find('a[data-glossary]').each(function() {
					$(this).fadeIn();
					var tab = $(this).data('glossary');
					var count = data.counters[tab];
					$(this).attr({'title': count, 'data-count': count});
					if (!count) {
						$(this).addClass('elgg-state-inactive elgg-state-disabled')
					}
				})

				$glossary.find('a[data-glossary]:not(.elgg-state-inactive):first').trigger('click');
			}
		})

	}

	framework.inbox.loadUserpickerTab = function(e) {

		e.preventDefault();

		var $elem = $(this);
		var $glossary = $(this).closest('.userpicker-glossary');
		var $panel = $elem.next('[data-glossary-info]');

		$('[data-glossary-info]', $glossary).not($panel).fadeOut();
		$panel.show();

		$('[data-glossary]', $glossary).removeClass('elgg-state-active');
		$elem.addClass('elgg-state-active');

		if ($elem.is('.elgg-state-inactive')) {
			return false;
		}

		var data = $glossary.data();

		elgg.post($elem.attr('href'), {
			data: data,
			dataType: 'json',
			beforeSend: function() {
				$panel.show().addClass('elgg-state-loading');
			},
			complete: function() {
				$panel.removeClass('elgg-state-loading');
			},
			success: function(data) {

				if (data.items) {
					$panel.append(data.items.join(''));
				}
				$elem.addClass('elgg-state-inactive');

			}
		})
	}

	elgg.register_hook_handler('init', 'system', framework.inbox.user);
	elgg.register_hook_handler('init', 'system', framework.inbox.userpicker);


<?php if (FALSE) : ?>
	</script>
<?php endif; ?>