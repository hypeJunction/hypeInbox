<div class="inbox-messages-controls">
	<div class="inbox-messages-control-group">
		<?php
		$params = $vars;
		$params['sort_by'] = 'priority';
		$params['class'] = 'inbox-menu elgg-menu-hz';
		echo elgg_view_menu('inbox', $params);
		?>
	</div>
</div>
