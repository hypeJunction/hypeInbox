<?php

return array(

	'admin:inbox' => 'Inbox Settings',
	'admin:inbox:message_types' => 'Message Types',

	'item:object:message:create' => 'Add new message type',
	'item:object:message:name' => 'Unique type name',
	
	'item:object:message:setting:multiple' => 'Allow multiple recipients',
	'item:object:message:setting:attachments' => 'Allow file attachments',
	'item:object:message:setting:persistent' => 'Make persistent (can not be deleted by recipient)',
	'item:object:message:setting:no_subject' => 'Disable subject line',
	'item:object:message:setting:policy' => 'Communication policy',
	'item:object:message:setting:policy:help' => 'Specifies sets of users between which this communication can occur.
		"Sender" and "Recipient" fields specify types of users (based on their "roles" on the site).
		"Relationship to recipient" specifies the type of relationship that must exist between the Sender and the Recipient for this communication to be permissible (e.g. Sender must be a friend of the Recipient).
		"Inverse relationship" specifies that the type of relationship should be inversed (e.g. Recipient must be a friend of the Sender for the Sender to be able to contact the Recipient)
		"Relationship of the sender to the group" creates an additional level of filtering, whereas 1) the Recipient must be a member of a group and 2) the Sender must have a specified relationship with that group (e.g. setting this to "member" would indicate that this type of communication can only occur between members of the same group)
	',

	'item:object:message:all' => 'All Messages',

	'inbox:admin:import' => 'Import older messages',
	'inbox:admin:import_stats' => '%s messages are lacking metadata information required for hypeInbox compatibility',
	'inbox:admin:import_start' => 'Start Import',
	'inbox:admin:import_complete' => 'Import complete',
	
	'inbox:user_type:all' => 'Any user',
	'inbox:user_type:admin' => 'Administrator',
	'inbox:user_type:editor' => 'Editor',
	'inbox:user_type:supervisor' => 'Supervisor',
	'inbox:user_type:observer' => 'Observer',

	'item:object:message:label:singular' => 'Label (singular)',
	'item:object:message:label:plural' => 'Label (plural)',

	'inbox:send' => 'Send %s',
	'inbox:sender' => 'Sender',
	'inbox:relationship' => 'Relationship to recipient',
	'inbox:recipient' => 'Recipient',
	'inbox:inverse_relationship' => 'Inverse relationship',
	'inbox:relationship' => 'Relationship to recipient',
	'inbox:group_relationship' => 'Relationship of the sender to the group that the recipient is a member of',

	'inbox' => 'Messages',
	'inbox:all' => 'All messages',
	'inbox:inbox' => 'Inbox',
	'inbox:sent' => 'Sent messages',
	'inbox:compose' => 'Compose',
	'inbox:compose:message_type' => 'Compose a %s',
	'inbox:reply' => 'Reply',
	'inbox:reply:message_type' => 'Reply to a %s',
	'inbox:reply:prefix' => 'Re:',
	'inbox:message_type' => '%s',
	'inbox:message_type:sent' => 'Sent %s',

	'inbox:conversation:user' => 'Conversation with %s',
	'inbox:conversation:group' => 'Group conversation',
	
	'inbox:usersettings:grouping' => 'Group inbox messages by sender',
	'inbox:group' => 'Group',
	'inbox:dontgroup' => 'Don\'t Group',

	'inbox:message_not_found' => 'Message not found',
	
	'inbox:untitled' => 'Untitled',
	'inbox:me' => 'me',
	'inbox:recipients:others' => '%s others',
	
	'inbox:thread' => 'View all %s messages in this thread',
	'inbox:thread:count' => '%s messages',
	'inbox:thread:unread' => '%s new',
	'inbox:thread:new' => 'New message',
	'inbox:thread:participants' => 'Members in this thread',

	'inbox:attachments:count' => '%s attachments',
	
	'inbox:message' => 'Message: %s',
	'inbox:conversation' => 'Conversation between you and %s',

	'inbox:nomessages' => 'There are no messages in this thread',

	'inbox:load:before' => 'Show previous %s messages',
	'inbox:load:after' => 'Show next %s messages',

	'inbox:delete' => 'Delete',
	'inbox:markread' => 'Mark as read',
	'inbox:markunread' => 'Mark as unread',
	
	'inbox:delete:success' => '%s messages were successfully deleted',
	'inbox:delete:success:single' => 'Message was successfully deleted',
	'inbox:delete:error' => 'Messages could not be deleted',
	'inbox:delete:inbox:confirm' => 'Are you sure you want to delete all selected messages?',
	'inbox:delete:thread:confirm' => 'Are you sure you want to delete all messages in this thread?',
	'inbox:delete:message:confirm' => 'Are you sure you want to delete this message?',
	
	'inbox:markread:success' => '%s messages were marked as read',
	'inbox:markread:success:single' => 'Message was marked as read',
	'inbox:markread:error' => 'Messages could not be marked as read',
	
	'inbox:markunread:success' => '%s messages were marked as unread',
	'inbox:markunread:success:single' => 'Message was marked as unread',
	'inbox:markunread:error' => 'Messages could not be marked as unread',
	
	'inbox:error:notfound' => '%s messages could not be found',
	'inbox:error:persistent' => '%s messages cound not be delete due to their read-only setting',
	'inbox:error:unknown' => '%s messages could not be deleted because of an unknown error',

	'inbox:send:success' => 'Message was successfully sent',
	'inbox:send:error:no_recipients' => 'No recipients were selected',
	'inbox:send:error:no_body' => 'You need add a message body',
	'inbox:send:error:generic' => 'An unknown error ocurred while sending the message',
	
	'inbox:user:unknown' => 'Unknown',
	'inbox:form:toggle_all' => 'Toggle all',
	
	'inbox:message:recipient' => 'Recipient',
	'inbox:message:recipients' => 'Recipients',
	'inbox:message:subject' => 'Subject',
	'inbox:message:body' => 'Message',
	'inbox:message:attachments' => 'Attachments',
	'inbox:message:attachments:add' => 'Add attachments',
	'inbox:message:send' => 'Send',
	
	'inbox:notification:subject' => 'You have a new %s',
	'inbox:notification:body' => "You have a new %s from %s. It reads:


	%s


	To view your messages, click here:

	%s

	To send %s a message, click here:

	%s

	Please do not reply to this email.",
);
