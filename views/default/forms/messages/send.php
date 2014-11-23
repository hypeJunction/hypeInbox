<?php
/**
 * Compose message form
 */

namespace hypeJunction\Inbox;

$entity = elgg_extract('entity', $vars, false);
$message_type = elgg_extract('message_type', $vars);
$recipient_guids = elgg_extract('recipient_guids', $vars, array());
$subject = elgg_extract('subject', $vars, '');
$message = elgg_extract('body', $vars, '');
$multiple = elgg_extract('multiple', $vars, false);
$has_subject = elgg_extract('has_subject', $vars, true);
$allows_attachments = elgg_extract('allows_attachments', $vars, false);
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
			'callback' => __NAMESPACE__ . '\\recipient_tokeninput_callback',
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
	?>
	<div class="inbox-form-row">
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
	echo elgg_view('input/submit', array(
		'value' => elgg_echo('inbox:message:send')
	));
	?>
</div>