<?php

use hypeJunction\Inbox\Actions\MarkAsRead;

$result = hypeApps()->actions->execute(new MarkAsRead());
forward($result->getForwardURL());