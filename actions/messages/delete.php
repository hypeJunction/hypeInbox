<?php

use hypeJunction\Inbox\Actions\DeleteMessage;

$result = hypeApps()->actions->execute(new DeleteMessage());
forward($result->getForwardURL());
