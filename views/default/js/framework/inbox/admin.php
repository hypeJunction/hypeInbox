<?php if (FALSE) : ?>
	<script type="text/javascript">
<?php endif; ?>

	elgg.provide('framework.inbox');

	framework.inbox.admin = function() {

		$('#inbox-admin-import')
		.click(function(e) {
			$('#import-progress').progressbar({
				value : 0
			});

			$(this).hide();

			var params = {
				count : $(this).data('count'),
				offset : 0,
				progress : 0,
				limit : 10
			}

			elgg.trigger_hook('import', 'framework:inbox', params);
		})
		
		$('.inbox-icon-plus')
		.live('click', function(e) {
			e.preventDefault();
			var $clone = $(this).closest('.inbox-policy').clone().hide();
			$clone.find('input').val('');
			$(this).closest('.inbox-policy').after($clone.fadeIn());
		})


		$('.inbox-icon-minus')
		.live('click', function(e) {
			e.preventDefault();
			if (!confirm(elgg.echo('question:areyousure'))) {
				return false;
			}
			$(this).closest('.inbox-policy').fadeOut().remove();
		})
	}


	framework.inbox.admin.import = function(hook, type, params) {

		elgg.action('action/inbox/admin/import', {
			data : params,
			success : function(data) {

				if (!data.output.complete) {
					params.offset = data.output.offset;
					params.progress = params.progress + params.limit;

					$('#import-progress').progressbar({
						value : params.progress * 100 / params.count
					})

					elgg.trigger_hook('import', 'framework:inbox', params);
				} else {
					elgg.system_message(elgg.echo('hj:inbox:admin:import_complete'));
					$('#import-progress').progressbar({
						value : 100
					})
					location.reload();
				}
			}
		})

	}


	elgg.register_hook_handler('init', 'system', framework.inbox.admin);
	elgg.register_hook_handler('import', 'framework:inbox', framework.inbox.admin.import);

<?php if (FALSE) : ?>
	</script>
<?php endif; ?>