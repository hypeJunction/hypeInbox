<?php

use hypeJunction\Inbox\Actions\SavePluginSettings;

$result = hypeApps()->actions->execute(new SavePluginSettings());
forward($result->getForwardURL());