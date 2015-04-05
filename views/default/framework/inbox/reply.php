<?php

use hypeJunction\Inbox\Message;

$entity = elgg_extract('entity', $vars, false);
if (!$entity instanceof Message) {
	return true;
}

elgg_push_context('inbox-reply');
echo '<div class="inbox-thread-reply-form" id="reply">';
$form_vars = hypeInbox()->model->prepareFormValues($entity->getParticipantGuids(), $entity->getMessageType(), $entity);
$form = elgg_view('framework/inbox/compose', $form_vars);
echo elgg_view_module('aside', elgg_echo('inbox:reply'), $form);
echo '</div>';
elgg_pop_context();