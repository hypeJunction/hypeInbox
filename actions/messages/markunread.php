<?php

use hypeJunction\Inbox\Actions\MarkAsUnread;

$result = hypeApps()->actions->execute(new MarkAsUnread());
forward($result->getForwardURL());