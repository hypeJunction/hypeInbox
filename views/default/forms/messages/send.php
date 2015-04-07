<?php
/**
 * Compose message form
 */

$entity = elgg_extract('entity', $vars, false);
$message_type = elgg_extract('message_type', $vars);
$recipient_guids = elgg_extract('recipient_guids', $vars, array());
$subject = elgg_extract('subject', $vars, '');
$message = elgg_extract('body', $vars, '');
$multiple = elgg_extract('multiple', $vars, false);
$has_subject = elgg_extract('has_subject', $vars, true);
$allows_attachments = elgg_extract('allows_attachments', $vars, false);

$footer_controls = array();
?>

<?php
if (!$entity) {
	?>
	<div class="inbox-form-row">
		<label><?php
			if ($multiple) {
				echo elgg_echo('inbox:message:recipients');
			} else {
				echo elgg_echo('inbox:message:recipient');
			}
			?></label>
		<?php
		echo elgg_view('input/tokeninput', array(
			'name' => 'recipient_guids',
			'value' => $recipient_guids,
			'multiple' => $multiple,
			'callback' => 'hypeJunction\\Inbox\\Search\\Recipients::search',
			'query' => array(
				'message_type' => $message_type,
			)
		));
		?>
	</div>
	<?php
} else {
	foreach ($recipient_guids as $guid) {
		echo elgg_view('input/hidden', array(
			'name' => 'recipient_guids[]',
			'value' => $guid,
		));
	}
}

if ($has_subject) {
	if (!$entity) {
		?>
		<div class="inbox-form-row">
			<label><?php echo elgg_echo('inbox:message:subject') ?></label>
			<?php
			echo elgg_view('input/text', array(
				'name' => 'subject',
				'value' => $subject
			));
			?>
		</div>
		<?php
	} else {
		echo elgg_view('input/hidden', array(
			'name' => 'subject',
			'value' => $entity->getReplySubject(),
		));
	}
}
?>
<div class="inbox-form-row">
	<label><?php echo elgg_echo('inbox:message:body') ?></label>
	<?php
	echo elgg_view('input/plaintext', array(
		'name' => 'body',
		'value' => $message,
		'rows' => 5,
	));
	?>
</div>
<?php
if ($allows_attachments && elgg_view_exists('input/dropzone')) {
	$footer_controls[] = elgg_view('output/url', array(
		'class' => 'inbox-toggle-attachments-form',
		'text' => elgg_echo('inbox:message:attachments:add'),
		'href' => 'javascript:void(0);'
			));
	?>
	<div class="inbox-form-row inbox-attachments-form hidden">
		<label><?php echo elgg_echo('inbox:message:attachments') ?></label>
		<?php
		echo elgg_view('input/dropzone', array(
			'name' => 'attachments',
			'max' => 25,
			'multiple' => true,
		));
		?>
	</div>
	<?php
}

$footer_controls[] = elgg_view('input/submit', array(
	'value' => elgg_echo('inbox:message:send')
		));
?>
<div class="inbox-form-row elgg-foot text-right">
	<?php
	echo elgg_view('input/hidden', array(
		'name' => 'message_type',
		'value' => $message_type,
	));
	echo elgg_view('input/hidden', array(
		'name' => 'guid',
		'value' => $entity->guid,
	));

	foreach ($footer_controls as $footer_control) {
		$controls .= elgg_format_element('li', array(), $footer_control);
	}
	echo elgg_format_element('ul', array(
		'class' => 'inbox-footer-controls',
			), $controls);
	?>
</div>