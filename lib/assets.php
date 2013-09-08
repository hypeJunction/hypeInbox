<?php

elgg_register_simplecache_view('css/framework/inbox/base');
elgg_register_css('inbox.base.css', elgg_get_simplecache_url('css', 'framework/inbox/base'));

elgg_register_simplecache_view('js/framework/inbox/admin');
elgg_register_js('inbox.admin.js', elgg_get_simplecache_url('js', 'framework/inbox/admin'));

elgg_register_simplecache_view('js/framework/inbox/user');
elgg_register_js('inbox.user.js', elgg_get_simplecache_url('js', 'framework/inbox/user'));

elgg_register_ajax_view('framework/inbox/thread/message');
elgg_register_ajax_view('framework/inbox/thread/after');
elgg_register_ajax_view('framework/inbox/thread/before');