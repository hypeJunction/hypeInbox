<?php

namespace hypeJunction\Inbox\Search;

class Recipients {

	public static function search($term) {
		return hypeInbox()->model->searchRecipients($term);
	}
}