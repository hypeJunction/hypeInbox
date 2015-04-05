<?php

use hypeJunction\Categories\Actions\SavePluginSettings;

$result = hypeCategories()->actions->execute(new SavePluginSettings());
forward($result->getForwardURL());