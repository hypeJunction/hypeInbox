<?php

use hypeJunction\Inbox\Message;

$subtypes = array(Message::SUBTYPE);

foreach ($subtypes as $subtype) {
	update_subtype('object', $subtype);
}