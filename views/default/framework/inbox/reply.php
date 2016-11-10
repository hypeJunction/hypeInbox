<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars, false);
if (!$entity instanceof Message) {
	return true;
}

elgg_push_context('inbox-reply');

$form_vars = hypeInbox()->model->prepareFormValues($entity->getParticipantGuids(), $entity->getMessageType(), $entity);

$form = elgg_view('framework/inbox/compose', $form_vars);

$user = elgg_get_logged_in_user_entity();
$size = elgg_extract('size', $vars, 'small');
$icon = elgg_view_entity_icon($user, $size, array(
	'use_hover' => elgg_extract('full_view', $vars, false),
));

$content = elgg_format_element('div', ['class' => 'inbox-message-icon'], $icon);
$content .= elgg_format_element('div', ['class' => 'inbox-message-content'], $form);

echo elgg_format_element('div', [
	'class' => 'inbox-thread-reply-form inbox-message',
	'id' => 'reply',
], $content);

elgg_pop_context();
