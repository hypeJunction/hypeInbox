define(['jquery', 'elgg'], function ($, elgg) {

	var inbox = {
		/**
		 * Bind events
		 * @returns {void}
		 */
		init: function () {
			if (elgg.config.inboxAdmin) {
				return;
			}
			$(document).on('click', '#inbox-admin-import', function (e) {
				e.preventDefault();

				$('#import-progress').progressbar({
					value: 0
				});

				$(this).hide();

				var params = {
					count: $(this).data('count'),
					offset: 0,
					progress: 0,
					limit: 10
				};

				inbox.importBatch(params);
			});

			$(document).on('click', '.inbox-icon-plus', function (e) {
				e.preventDefault();
				var $clone = $(this).closest('.inbox-policy').clone().hide();
				$clone.find('input').val('');
				$(this).closest('.inbox-policy').after($clone.fadeIn());
			});

			$(document).on('click', '.inbox-icon-minus', function (e) {
				e.preventDefault();
				if (!confirm(elgg.echo('question:areyousure'))) {
					return false;
				}
				$(this).closest('.inbox-policy').fadeOut().remove();
			});
			
			elgg.config.inboxAdmin = true;
		},
		/**
		 * Import batch and provide feedback
		 * @param {object} params
		 * @returns {void}
		 */
		importBatch: function (params) {
			elgg.action('action/inbox/admin/import', {
				data: params,
				success: function (data) {
					if (!data.output.complete) {
						params.offset = data.output.offset;
						params.progress = params.progress + params.limit;

						$('#import-progress').progressbar({
							value: params.progress * 100 / params.count
						});
						inbox.importBatch(params);
					} else {
						elgg.system_message(elgg.echo('inbox:admin:import_complete'));
						$('#import-progress').progressbar({
							value: 100
						});
						location.reload();
					}
				}
			});
		}
	};

	inbox.init();
});


