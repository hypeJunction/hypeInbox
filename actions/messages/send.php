<?php

use hypeJunction\Inbox\Actions\SendMessage;

$result = hypeApps()->actions->execute(new SendMessage());
forward($result->getForwardURL());