<?php if (false) : ?><style type="text/css"><?php endif; ?>

	<?php $path = elgg_get_site_url() . 'mod/hypeInbox/graphics/' ?>

	[class*="inbox-icon-"] {
		height:22px;
		width:22px;
		display:inline-block;
		vertical-align:middle;
		background-position:50% 50%;
		background-repeat:no-repeat;
		background-size:100%;
		opacity:0.5;
	}
	[class*="inbox-icon-"].icon-small {
		height:16px;
		width:16px;
	}
	[class*="inbox-icon-"] + span {
		margin-left: 5px;
		display: inline-block;
		vertical-align: middle;
		line-height: 26px;
	}
	[class*="inbox-icon-"].icon-small + span {
		line-height: 16px;
	}
	.inbox-icon-plus {
		background-image:url(<?php echo $path ?>icons/plus.png);
	}
	.inbox-icon-minus {
		background-image:url(<?php echo $path ?>icons/minus.png);
	}
	.inbox-icon-delete {
		background-image:url(<?php echo $path ?>icons/delete.png);
	}
	.inbox-icon-markread {
		background-image:url(<?php echo $path ?>icons/markread.png);
	}
	.inbox-icon-markunread {
		background-image:url(<?php echo $path ?>icons/markunread.png);
	}
	.inbox-icon-message-plus {
		background-image:url(<?php echo $path ?>icons/message-plus.png);
	}

	.elgg-state-loading [class*="inbox-icon-"],
	[class*="inbox-icon-"].elgg-state-loading	{
		background-image:url(<?php echo $path ?>icon-loader.gif);
	}

	.inbox-policy {
		border-top:1px solid #e8e8e8
	}
	.inbox-policy:nth-child(even) {
		background:#f4f4f4;
	}
	.inbox-policy [class*="inbox-icon-"] {
		float: right;
		margin:0 5px;
	}
	.inbox-policy .elgg-col {
		padding:5px;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
		box-sizing:border-box;
	}
	.inbox-unread-count {
		padding: 0px 3px;
		display: inline-block;
		background: dodgerblue;
		color: white;
		font-size: 10px;
		font-weight: bold;
		-webkit-border-radius: 3px;
	}

	.inbox-messages-table {
		font-size: 0.9em;
		border-top:1px solid #e8e8e8;
	}
	.inbox-messages-table > li {
		border-bottom:1px solid #e8e8e8;
	}
	.inbox-message {
		cursor: pointer;
		border-left:4px solid #e8e8e8;
		margin:2px 0;
	}
	.inbox-message:hover {
		background-color:#f4f4f4;
	}
	.inbox-message.inbox-message-thread-unread {
		border-left: 4px solid dodgerblue;
	}
	.inbox-message.inbox-thread-message-summary {
		background-color: #f4f4f4;
		color: #999;
	}
	.inbox-message.inbox-thread-message-full {
		cursor:default;
	}
	.inbox-message.inbox-thread-message-full:hover {
		background-color:white;
	}
	.inbox-message > div {
		display:inline-block;
	}
	.inbox-message > div {
		display: inline-block;
		vertical-align: top;
		padding: 5px;
	}
	.inbox-message-checkbox {
		width: 2%;
	}
	.inbox-message .inbox-message-checkbox {
		margin-top: 5px;
	}
	.inbox-messages-table .inbox-message-conversation {
		width: 25%;
	}
	.inbox-messages-table .inbox-message-thread {
		width: 6%;
		text-align: right;
	}
	.inbox-message .inbox-message-title {
		width: 35%;
	}
	.inbox-message .inbox-message-title a {
		color:inherit;
	}
	.inbox-message .inbox-message-attachments {
		width: 10%;
	}
	.inbox-message .inbox-message-menu {
		font-size: 0.9em;
		width: 10%;
	}
	.inbox-message .elgg-menu-entity {
		margin-left:0;
		height:auto;
	}
	.inbox-conversation-user {
		display: inline-block;
		margin: 2px;
		line-height: 16px;
		vertical-align: middle;
		padding: 3px;
		background: #f4f4f4;
		border: 1px solid #e8e8e8;
	}
	.inbox-conversation-user img {
		display: inline;
		vertical-align: middle;
		margin: 0 8px 0 0;
	}

	.inbox-message-thread-count {
		text-decoration:none;
		padding:2px 6px;
		background:#f4f4f4;
		border:1px solid #e8e8e8;
		color:#666;
		font-weight:bold;
		display:block;
		text-align:center;
	}
	.inbox-message-thread-unread-count {
		background: dodgerblue;
		border: 1px solid dodgerblue;
		color: white;
	}
	.inbox-message-thread-unread-count.hidden {
		display: none;
	}
	.userpicker-glossary {
		border: 1px solid #e8e8e8;
		background:#f4f4f4;
		position: relative;
		width: 100%;
		height: 350px;
		z-index: 100;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		overflow: hidden;
	}
	.userpicker-glossary.elgg-state-loading:after,
	.userpicker-glossary .elgg-tabs > li.elgg-tab > .elgg-content.elgg-state-loading:before {
		width: 100%;
		height: 100%;
		content: "";
		display: block;
		position: absolute;
		top: 0;
		left: 0;
		background:url(<?php echo $path ?>loader.gif) no-repeat 50% 50%;
		background-color: #FFF;
		background-color: rgba(255,255,255,0.8);
		z-index: 101;
	}
	.userpicker-glossary .elgg-tabs {
		height: 1;
		margin: 0;
		position: relative;
		width: 100%;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		-box-sizing: border-box;
		padding: 8px;
		height: 100%;
	}
	.userpicker-glossary .elgg-tabs > li.elgg-tab, .userpicker-glossary .elgg-tabs > li.elgg-tab:hover {
		display: inline-block;
		float: none;
		border: 0;
		background: 0;
		border-radius: 0;
		width: 2.9%;
		top: 0;
		-webkit-box-sizing: border-box;
		-moz-box-sizing: border-box;
		box-sizing: border-box;
		margin: 0.3%;
		vertical-align: middle;
	}
	.userpicker-glossary .elgg-tabs > li.elgg-tab > a, .userpicker-glossary .elgg-tabs > li.elgg-tab > a:hover {
		width: 100%;
		height: 22px;
		display:block;
		text-align: center;
		padding: 5px 0;
		text-transform: uppercase;
		line-height: 20px;
		font-weight: bold;
		background: #efefef;
		color: #666;
	}
	.userpicker-glossary .elgg-tabs > li.elgg-tab > a.elgg-state-active, .userpicker-glossary .elgg-tabs > li.elgg-tab > a:hover {
		background: #e8e8e8;
		color: #4690D6;
	}
	.userpicker-glossary .elgg-tabs > li.elgg-tab > a.elgg-state-disabled, .userpicker-glossary .elgg-tabs > li.elgg-tab > a.elgg-state-disabled:hover {
		background: #f4f4f4;
		color: #bbb;
	}
	.userpicker-glossary .elgg-tabs > li.elgg-tab > .elgg-content {
		position: absolute;
		background: #fff;
		width: 100%;
		height:300px;
		left: 0;
		top: 0;
		margin:50px auto 0;
		display: none;
		overflow:auto;
		text-align:left;
	}

	.userpicker-glossary .userpicker-image-block {
		display:inline-block;
		margin: 10px 0.5%;
		width:32%;
		padding:4px;
		border:1px solid #e8e8e8;
		background:#f4f4f4;
		-webkit-box-sizing:border-box;
		-moz-box-sizing:border-box;
		box-sizing:border-box;
		position:relative;
	}

	.userpicker-glossary .userpicker-image-block > .elgg-image,
	.userpicker-glossary .userpicker-image-block > .elgg-body
	{
		display:inline-block;
		vertical-align:top;
		margin:3px;
	}
	.userpicker-glossary .userpicker-image-block > .elgg-image-alt {
		position:absolute;
		right:3px;
		top:3px;
	}
	.userpicker-glossary .userpicker-image-block a, .userpicker-glossary .userpicker-image-block a:hover {
		padding:0;
		background:none;
		border:none;
	}

	.elgg-module-messages-attachments img {
		max-width: 25px;
	}
	.elgg-module-messages-attachments img {
		max-width: 25px;
	}
	.elgg-module-messages-reply {
		margin-top: 30px;
	}
	.elgg-module-messages-reply > .elgg-body {
		padding: 20px;
		background: #f4f4f4;
		border: 1px solid #e8e8e8;
		margin: 10px 0;
	}

	.inbox-thread-load-before.elgg-state-loading,
	.inbox-thread-load-after.elgg-state-loading {
		padding-left:30px;
		background: transparent url(<?php echo $path ?>bar.gif) 0 50% no-repeat;
	}
	
	<?php if (false) : ?></style><?php endif; ?>